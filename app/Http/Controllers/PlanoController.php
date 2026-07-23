<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PlanoController extends Controller
{
    // ✅ Esse método está faltando ou com nome errado?
    public function index()
    {
        $clinica = auth()->user()->clinica;
        return view('planos.index', compact('clinica'));
    }
    //
    public function criarAssinatura(Request $request)
    {
        $clinica = auth()->user()->clinica;
        $request->validate([
            'forma_pagamento' => 'required|in:CREDIT_CARD,PIX,BOLETO'
        ]);

        // ✅ PASSO 1: Cria cliente NO ASAAS SE NÃO EXISTIR
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
                return back()->withErrors(['Erro ao conectar com o sistema de pagamento.']);
            }

            $resp = $cliente->json();
            $clinica->update(['asaas_customer' => $resp['id']]);
        }

        // ✅ PASSO 2: Cria assinatura
        $assinatura = Http::withHeaders([
            'access_token' => env('ASAAS_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.asaas.com/v3/subscriptions', [
            'customer' => $clinica->asaas_customer,
            'billingType' => $request->forma_pagamento,
            'value' => 150.00,
            'cycle' => 'MONTHLY',
            'description' => 'Plano Profissional DentistaTech',
            'nextDueDate' => $clinica->trial_ends_at?->format('Y-m-d') ?? now()->addDays(15)->format('Y-m-d'),
        ]);

        if (!$assinatura->successful()) {
            return back()->withErrors(['Não foi possível gerar a assinatura. Tente novamente.']);
        }

        $dados = $assinatura->json();

        // ✅ PASSO 3: Atualiza status local
        $clinica->update([
            'asaas_subscription' => $dados['id'],
            'status_assinatura' => 'ativa',
            'trial_ends_at' => null,
        ]);

        // Redireciona conforme forma de pagamento
        if ($request->forma_pagamento === 'CREDIT_CARD' && isset($dados['paymentLink'])) {
            return redirect()->away($dados['paymentLink']);
        }

        return redirect()->route('dashboard')->with('sucesso', 'Assinatura criada com sucesso!');
    }
}
