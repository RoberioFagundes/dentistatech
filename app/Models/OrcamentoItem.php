<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrcamentoItem extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela no plural
    protected $table = "orcamento_itens";

    protected $fillable = [
        'dente',       // ID do orçamento pai (OrcamentoPaciente)
        'face',    // ID do procedimento/serviço realizado
        'quantidade',         // Quantidade do procedimento (Ex: 2 extrações)
        'status',
        'orcamento_paciente_id',     // Preço do procedimento no momento do orçamento
        'user_id',           // Valor ou porcentagem de desconto aplicado ao item
        'procedimento_dente_id',        // Valor líquido (Quantidade * Valor Unitário - Desconto)
        'orcamento_paciente_id',
        'valor'
    ];

    /**
     * Define a conversão de tipos de dados nativos para garantir a precisão matemática.
     */
    protected $casts = [
        'quantidade' => 'integer',
        'valor' => 'float',
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