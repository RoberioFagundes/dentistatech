<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Exception;

/*pacote para instalar asaas 
composer require adrianovcar/asaas-php-sdk
*/

class AssinaturaController extends Controller
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('ASAAS_API_KEY');
        $this->baseUrl = env('ASAAS_ENVIRONMENT', 'sandbox') === 'sandbox'
            ? 'https://sandbox.asaas.com/api/v3'
            : 'https://api.asaas.com/v3';
    }

    private function obterOuCriarClienteAsaas(Clinica $clinica): string
    {
        if (!empty($clinica->asaas_customer)) {
            return $clinica->asaas_customer;
        }

        try {
            $resp = Http::withHeaders(['access_token' => $this->apiKey])
                ->post("{$this->baseUrl}/customers", [
                    'name' => $clinica->razao_social,
                    'email' => $clinica->usuario?->email ?? Auth::user()->email,
                    'cpfCnpj' => preg_replace('/\D/', '', $clinica->cnpj),
                    'phone' => preg_replace('/\D/', '', $clinica->telefone),
                    'mobilePhone' => preg_replace('/\D/', '', $clinica->telefone),
                    'address' => $clinica->endereco,
                    'postalCode' => preg_replace('/\D/', '', $clinica->cep ?? '00000000'),
                    'addressNumber' => $clinica->numero ?? 'S/N',
                    'externalReference' => (string) $clinica->id
                ]);

            if (!$resp->successful()) {
                $erro = $resp->json('errors.0.description', 'Erro desconhecido');
                throw new Exception($erro);
            }

            $cliente = $resp->object();
            $clinica->update(['asaas_customer' => $cliente->id]);
            return $cliente->id;
        } catch (Exception $e) {
            throw new Exception('Erro ao cadastrar cliente: ' . $e->getMessage());
        }
    }

    public function criar(Request $request)
    {
        try {
            $usuario = Auth::user();
            $clinica = Clinica::findOrFail($usuario->clinica_id);

            $formaPagamentoStr = $request->forma_pagamento;

            if (!in_array($formaPagamentoStr, ['CREDIT_CARD', 'PIX', 'BOLETO'])) {
                return back()->withErrors(['forma_pagamento' => 'Forma de pagamento inválida.'])->withInput();
            }

            $valorAssinatura = 150.00;
            $dataVencimento = now()->format('Y-m-d'); // Pagamento imediato

            $idCliente = $this->obterOuCriarClienteAsaas($clinica);

            $dados = [
                'customer' => $idCliente,
                'billingType' => $formaPagamentoStr,
                'value' => $valorAssinatura,
                'dueDate' => $dataVencimento,
                'description' => 'Assinatura Mensal - Plano Profissional DentistaTech'
            ];

            // Dados do cartão (já funcionando)
            if ($formaPagamentoStr === 'CREDIT_CARD') {
                $request->validate([
                    'holder_name'  => 'required|string',
                    'number'        => 'required|string|max:20',
                    'expiry'        => ['required', 'regex:/^(0[1-9]|1[0-2])\/(\d{2}|\d{4})$/'],
                    'ccv'           => 'required|digits_between:3,4',
                ], [], [
                    'holder_name' => 'nome no cartão',
                    'number' => 'número do cartão',
                    'expiry' => 'validade',
                    'ccv' => 'CVV'
                ]);

                [$mes, $ano] = explode('/', $request->expiry);
                $ano = strlen(trim($ano)) === 2 ? '20' . trim($ano) : trim($ano);

                $dados['creditCard'] = [
                    'holderName' => $request->holder_name,
                    'number' => preg_replace('/\D/', '', $request->number),
                    'expiryMonth' => trim($mes),
                    'expiryYear' => $ano,
                    'ccv' => preg_replace('/\D/', '', $request->ccv)
                ];

                $dados['creditCardHolderInfo'] = [
                    'name' => $clinica->razao_social,
                    'email' => $clinica->usuario?->email ?? Auth::user()->email,
                    'cpfCnpj' => preg_replace('/\D/', '', $clinica->cnpj),
                    'postalCode' => preg_replace('/\D/', '', $clinica->cep ?? '00000000'),
                    'addressNumber' => $clinica->numero ?? 'S/N',
                    'phone' => preg_replace('/\D/', '', $clinica->telefone)
                ];
            }

            // Cria cobrança
            $resp = Http::withHeaders(['access_token' => $this->apiKey])
                ->post("{$this->baseUrl}/payments", $dados);

            if (!$resp->successful()) {
                $erro = $resp->json('errors.0.description', 'Erro ao gerar cobrança');
                throw new Exception($erro);
            }

            $cobranca = $resp->object();

            // Na criação da cobrança — ajuste o vencimento:
            $dataVencimento = now()->format('Y-m-d'); // HOJE = pagamento imediato

            // ... depois de criar $cobranca ...

            // 🟢 BUSCAR QR CODE OBRIGATORIAMENTE
            if ($formaPagamentoStr === 'PIX') {
                $url = "{$this->baseUrl}/payments/{$cobranca->id}/pixQrCode";

                $respPix = Http::withHeaders([
                    'access_token' => $this->apiKey,
                    'Accept' => 'application/json',
                ])->get($url);

                // Adicione isso temporariamente para debugar na tela:
                // dd($url, $respPix->status(), $respPix->json());

                if ($respPix->successful()) {
                    $cobranca->pix = $respPix->object();
                } else {
                    logger()->error('Falha QR Code - Status: ' . $respPix->status() . ' | Resposta: ' . $respPix->body());
                }
            }
            // 🟢 BOLETO: link já vem na resposta — só garante que existe
            if ($formaPagamentoStr === 'BOLETO' && empty($cobranca->bankSlipUrl)) {
                throw new Exception('Não foi possível gerar o link do boleto.');
            }



            $clinica->update([
                'status_assinatura'  => $cobranca->status === 'CONFIRMED' ? 'ativa' : 'trial', // Mudou de 'pendente' para 'trial' ou 'ativa'
                'asaas_subscription' => $cobranca->id,
                'trial_ends_at'       => now()->addDays(15),
            ]);

            return redirect()->route('assinatura.confirmar')
                ->with('cobranca', $cobranca)
                ->with('tipo', strtolower($formaPagamentoStr))
                ->with('valor', number_format($valorAssinatura, 2, ',', '.'))
                ->with('sucesso', 'Pagamento gerado com sucesso!');
        } catch (Exception $e) {
            return back()->withErrors(['erro' => 'Erro: ' . $e->getMessage()])->withInput();
        }
    }

    public function confirmar()
    {
        $cobranca = session('cobranca');
        $tipo = session('tipo');
        $valor = session('valor');

        if (!$cobranca || !$tipo) {
            return redirect()->route('planos')->with('erro', 'Cobrança não encontrada ou expirada.');
        }

        $usuario = Auth::user();
        $clinica = Clinica::find($usuario->clinica_id);

        return view('planos.confirmar', compact('cobranca', 'tipo', 'valor', 'clinica'));
    }
}
