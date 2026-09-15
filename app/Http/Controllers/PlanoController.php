<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanoController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $clinica = Clinica::findOrFail($usuario->clinica_id);

        return view('planos.index', compact('clinica'));
    }

    public function criarAssinatura(Request $request)
    {
        $clinica = auth()->user()->clinica;

        // 1. Validação dinâmica baseada na forma de pagamento
        $rules = [
            'forma_pagamento' => 'required|in:CREDIT_CARD,PIX,BOLETO'
        ];

        if ($request->forma_pagamento === 'CREDIT_CARD') {
            $rules['holder_name'] = 'required|string';
            $rules['number'] = 'required|string';
            $rules['expiry'] = 'required|string'; // formato MM/AA
            $rules['ccv'] = 'required|string';
        }

        $request->validate($rules);

        // 2. PASSO 1: Cria cliente NO ASAAS SE NÃO EXISTIR
        if (!$clinica->asaas_customer) {
            $cliente = Http::withHeaders([
                'access_token' => env('ASAAS_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.asaas.com/v3/customers', [
                'name' => $clinica->razao_social,
                'email' => auth()->user()->email,
                'phone' => preg_replace('/\D/', '', $clinica->telefone),
                'cpfCnpj' => preg_replace('/\D/', '', $clinica->cnpj),
            ]);

            if (!$cliente->successful()) {
                return back()->withErrors(['Erro ao conectar com o sistema de pagamento para cadastrar o cliente.']);
            }

            $resp = $cliente->json();
            $clinica->update(['asaas_customer' => $resp['id']]);
        }

        // 3. PASSO 2: Monta os dados para criar a Assinatura
        $dadosAssinatura = [
            'customer' => $clinica->asaas_customer,
            'billingType' => $request->forma_pagamento,
            'value' => 150.00,
            'cycle' => 'MONTHLY',
            'description' => 'Plano Profissional DentistaTech',
            'nextDueDate' => $clinica->trial_ends_at?->format('Y-m-d') ?? now()->addDays(15)->format('Y-m-d'),
        ];

        // Se for Cartão de Crédito, tratamos o mês e o ano de validade e enviamos os dados do cartão
        if ($request->forma_pagamento === 'CREDIT_CARD') {
            [$month, $year] = explode('/', $request->expiry);

            // Dados do cartão exigidos pelo Asaas para cobrança/assinatura direta
            $dadosAssinatura['creditCard'] = [
                'holderName' => $request->holder_name,
                'number' => preg_replace('/\D/', '', $request->number),
                'expiryMonth' => trim($month),
                'expiryYear' => strlen(trim($year)) == 2 ? '20' . trim($year) : trim($year),
                'ccv' => $request->ccv,
            ];

            // O Asaas exige também os dados do titular do cartão (remoteIp, cpfCnpj, etc. dependendo da versão, mas o básico do holder costuma passar)
            $dadosAssinatura['creditCardHolderInfo'] = [
                'name' => $request->holder_name,
                'email' => auth()->user()->email,
                'cpfCnpj' => preg_replace('/\D/', '', $clinica->cnpj),
                'postalCode' => preg_replace('/\D/', '', $clinica->cep ?? '00000000'),
                'addressNumber' => $clinica->numero ?? 'S/N',
                'phone' => preg_replace('/\D/', '', $clinica->telefone),
            ];

            $dadosAssinatura['remoteIp'] = $request->ip();
        }

        $assinatura = Http::withHeaders([
            'access_token' => env('ASAAS_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.asaas.com/v3/subscriptions', $dadosAssinatura);

        if (!$assinatura->successful()) {
            $erroDetalhe = $assinatura->json()['errors'][0]['description'] ?? 'Não foi possível gerar a assinatura.';
            return back()->withErrors([$erroDetalhe]);
        }

        $dados = $assinatura->json();

        // 4. PASSO 3: Atualiza status local
        $clinica->update([
            'asaas_subscription' => $dados['id'],
            'status_assinatura' => 'ativa',
            'trial_ends_at' => null,
        ]);

        // 5. PASSO 4: Redirecionamentos específicos por forma de pagamento

        // Se for Cartão e foi processado com sucesso
        if ($request->forma_pagamento === 'CREDIT_CARD') {
            return redirect()->route('dashboard')->with('sucesso', 'Assinatura via Cartão realizada com sucesso!');
        }

        // Se for Pix ou Boleto, a assinatura cria uma cobrança inicial. Vamos buscar a fatura/cobrança gerada para exibir ao usuário
        $cobrancas = Http::withHeaders([
            'access_token' => env('ASAAS_API_KEY'),
        ])->get("https://api.asaas.com/v3/subscriptions/{$dados['id']}/payments");

        if ($cobrancas->successful() && count($cobrancas->json()['data']) > 0) {
            $primeiraCobranca = $cobrancas->json()['data'][0];
            $cobrancaId = $primeiraCobranca['id'];

            // Se for PIX, pegamos o QR Code
            if ($request->forma_pagamento === 'PIX') {
                $pixQrCode = Http::withHeaders([
                    'access_token' => env('ASAAS_API_KEY'),
                ])->get("https://api.asaas.com/v3/payments/{$cobrancaId}/pixQrCode");

                if ($pixQrCode->successful()) {
                    return view('planos.pagamento-pix', [
                        'pix' => $pixQrCode->json(),
                        'cobranca' => $primeiraCobranca
                    ]);
                }
            }

            // Se for BOLETO, redirecionamos para o link da fatura/boleto do Asaas
            if ($request->forma_pagamento === 'BOLETO' && isset($primeiraCobranca['bankSlipUrl'])) {
                return redirect()->away($primeiraCobranca['bankSlipUrl']);
            }
        }

        return redirect()->route('dashboard')->with('sucesso', 'Assinatura criada com sucesso! Verifique seu e-mail para efetuar o pagamento.');
    }
}
