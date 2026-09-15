<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Clinica;
use Illuminate\Support\Facades\Log;

class WebhookAsaasController extends Controller
{
    //
  

    public function assinar(Request $request)
    {
        $clinica = auth()->user()->clinica;

        // 🔹 Passo 1: Criar cliente no Asaas (se ainda não existir)
        if (!$clinica->asaas_customer) {
            $cliente = Http::withHeaders([
                'access_token' => env('ASAAS_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.asaas.com/v3/customers', [
                'name' => $clinica->razao_social,
                'email' => $request->email,
                'phone' => $clinica->telefone,
                'cpfCnpj' => $clinica->cnpj,
            ]);

            $resposta = $cliente->json();
            $clinica->asaas_customer = $resposta['id'];
        }

        // 🔹 Passo 2: Criar assinatura mensal
        $assinatura = Http::withHeaders([
            'access_token' => env('ASAAS_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.asaas.com/v3/subscriptions', [
            'customer' => $clinica->asaas_customer,
            'billingType' => $request->forma_pagamento, // BOLETO, CREDIT_CARD, PIX
            'value' => 150.00,
            'cycle' => 'MONTHLY',
            'description' => 'Plano Profissional DentistaTech',
            // 🚩 IMPORTANTE: Primeira cobrança DEPOIS dos 15 dias
            'nextDueDate' => $clinica->trial_ends_at->format('Y-m-d'),
        ]);

        $dadosAssinatura = $assinatura->json();

        // 🔹 Passo 3: Atualizar dados da clínica
        $clinica->update([
            'asaas_subscription' => $dadosAssinatura['id'],
            'status_assinatura' => 'ativa',
            'trial_ends_at' => null, // Encerra o período de teste
        ]);

        return redirect()->route('dashboard')->with('sucesso', 'Assinatura realizada com sucesso!');
    }

    public function receber(Request $request)
    {
        // Valida o token de segurança (coloque no .env)
        if ($request->header('asaas-access-token') !== env('ASAAS_WEBHOOK_TOKEN')) {
            abort(403, 'Acesso negado');
        }

        $evento = $request->event;
        $dados = $request->all();

        Log::info("Webhook Asaas: {$evento}", $dados);

        match ($evento) {
            // ✅ Pagamento confirmado → Ativa o plano
            'PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED' => $this->pagamentoConfirmado($dados),

            // ⚠️ Pagamento atrasado → Avisa e bloqueia
            'PAYMENT_OVERDUE' => $this->pagamentoAtrasado($dados),

            // ❌ Pagamento cancelado/estornado → Bloqueia
            'PAYMENT_DELETED', 'PAYMENT_REFUNDED' => $this->pagamentoCancelado($dados),

            // 🔄 Assinatura atualizada
            'SUBSCRIPTION_UPDATED' => $this->atualizarAssinatura($dados),

            default => Log::info("Evento ignorado: {$evento}"),
        };

        return response()->json(['status' => 'ok'], 200);
    }

    protected function pagamentoConfirmado($dados)
    {
        $clinica = Clinica::where('asaas_customer', $dados['payment']['customer'])->first();
        if ($clinica) {
            $clinica->update(['status_assinatura' => 'ativa']);
        }
    }

    protected function pagamentoAtrasado($dados)
    {
        $clinica = Clinica::where('asaas_customer', $dados['payment']['customer'])->first();
        if ($clinica) {
            $clinica->update(['status_assinatura' => 'vencida']);
            // Aqui você pode enviar e-mail/WhatsApp avisando
        }
    }

    protected function pagamentoCancelado($dados)
    {
        $clinica = Clinica::where('asaas_customer', $dados['payment']['customer'])->first();
        if ($clinica) {
            $clinica->update(['status_assinatura' => 'cancelada']);
        }
    }

    protected function atualizarAssinatura($dados)
    {
        $clinica = Clinica::where('asaas_subscription', $dados['subscription']['id'])->first();
        if ($clinica) {
            $clinica->update([
                'status_assinatura' => match ($dados['subscription']['status']) {
                    'ACTIVE' => 'ativa',
                    'INACTIVE' => 'cancelada',
                    default => 'vencida'
                }
            ]);
        }
    }
}
