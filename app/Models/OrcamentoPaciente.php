<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrcamentoPaciente extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela no plural
    protected $table = "orcamento_pacientes";	

    protected $fillable = [
        'validade',
        'valor',
        'total_orcamento',
        'desconto',       // Soma do valor original de todos os itens
        'valor_liquido',   // Desconto aplicado sobre o total do orçamento
        'paciente_id',     // Valor final a ser pago (Bruto - Desconto)
        'atendimento_id',
        'user_id',            // Ex: 'Pendente', 'Aprovado', 'Recusado', 'Finalizado'
        'procedimento_dentes_id'
    ];

    /**
     * Define a conversão de tipos para garantir a consistência dos valores financeiros.
     */
    protected $casts = [
        'valor_bruto' => 'float',
        'desconto_global' => 'float',
        'valor_liquido' => 'float',
    ];

    /**
     * Retorna o Paciente para quem este orçamento foi gerado.
     * (Um orçamento PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Retorna o Dentista responsável pelo planejamento deste orçamento.
     * (Um orçamento PERTENCE A um dentista)
     */
    public function dente_responsavel(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    /**
     * Retorna todos os itens/procedimentos que compõem este orçamento.
     * (Um orçamento POSSUI MUITOS itens detalhados)
     */
    public function itens(): HasMany
    {
        return $this->hasMany(OrcamentoItem::class, 'orcamento_id');
    }
}