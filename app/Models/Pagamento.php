<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pagamento extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela
    protected $table = "pagamentos";

    protected $fillable = [
        'paciente_id',
        'orcamento_id',       // ID do orçamento pai (OrcamentoPaciente)
        'forma_pagamento_id', // ID da modalidade (Pix, Crédito, etc.)
        'valor_pago',
        'user_id',
        'data_pagamento',
        'status_pagamento',   // Ex: 'Pendente', 'Pago', 'Estornado'
        'observacoes'
    ];

    /**
     * Garante a conversão de tipos de dados para o PHP.
     */
    protected $casts = [
        'valor_pago' => 'float',
        'data_pagamento' => 'datetime', // Inclui data e hora do recebimento
    ];

    /**
     * Retorna o Paciente que realizou este pagamento.
     * (Um pagamento PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Retorna o Orçamento/Plano de Tratamento vinculado a este pagamento.
     * (Um pagamento PERTENCE A um orçamento de paciente)
     */
    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(OrcamentoPaciente::class, 'orcamento_id');
    }

    /**
     * Retorna a Forma de Pagamento escolhida para esta transação.
     * (Um pagamento PERTENCE A uma forma de pagamento)
     */
    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }
}