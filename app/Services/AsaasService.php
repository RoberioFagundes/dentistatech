<?php

namespace App\Services;

use LumenSistemas\Asaas\Facades\Asaas;

class AsaasService
{
    public function criarCliente($dados)
    {
        return Asaas::customers()->create([
            "name" => $dados['nome'],
            "email" => $dados['email'],
            "cpfCnpj" => $dados['cpf'],
            "phone" => $dados['telefone']
        ]);
    }

    public function criarAssinatura($cliente, $plano, $dadosPagamento = [])
    {
        $billingType = $dadosPagamento['billingType'] ?? 'PIX';

        $payload = [
            "customer" => $cliente['id'] ?? $cliente->id,
            "billingType" => $billingType,
            "value" => $plano->valor,
            "cycle" => "MONTHLY",
            "nextDueDate" => now()->format('Y-m-d'),
            "description" => "Assinatura - " . $plano->nome,
        ];

        if ($billingType === 'CREDIT_CARD' && isset($dadosPagamento['creditCard'])) {
            $payload['creditCard'] = $dadosPagamento['creditCard'];
            $payload['creditCardHolderInfo'] = $dadosPagamento['creditCardHolderInfo'];
        }

        return Asaas::subscriptions()->create($payload);
    }
}