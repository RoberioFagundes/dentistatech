<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrcamentoItem extends Model
{
    // Define explicitamente o nome da tabela no plural
    protected $table = "orcamento_itens";

    protected $fillable = [
        'orcamento_id',       // ID do orçamento pai (OrcamentoPaciente)
        'procedimento_id',    // ID do procedimento/serviço realizado
        'quantidade',         // Quantidade do procedimento (Ex: 2 extrações)
        'valor_unitario',     // Preço do procedimento no momento do orçamento
        'desconto',           // Valor ou porcentagem de desconto aplicado ao item
        'valor_total',        // Valor líquido (Quantidade * Valor Unitário - Desconto)
        'dente_regiao'        // Opcional: Indica se o procedimento é para um dente específico (Ex: 16)
    ];

    /**
     * Define a conversão de tipos de dados nativos para garantir a precisão matemática.
     */
    protected $casts = [
        'quantidade' => 'integer',
        'valor_unitario' => 'float',
        'desconto' => 'float',
        'valor_total' => 'float',
    ];

    /**
     * Retorna o Orçamento Geral (Pai) ao qual este item pertence.
     * (Um item de orçamento PERTENCE A um orçamento de paciente)
     */
    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(OrcamentoPaciente::class, 'orcamento_id');
    }

    /**
     * Retorna as informações do Procedimento/Serviço padrão associado a este item.
     * (Um item de orçamento PERTENCE A um procedimento do catálogo da clínica)
     */
    public function procedimento(): BelongsTo
    {
        return $this->belongsTo(Procedimento::class, 'procedimento_id');
    }
}