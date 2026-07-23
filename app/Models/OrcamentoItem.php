<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrcamentoItem extends Model
{
    use HasFactory;

    protected $table = 'orcamento_items';

    protected $fillable = [
        'dente',
        'face',
        'quantidade',
        'status',
        'orcamento_paciente_id',
        'user_id',
        'procedimento_dente_id',
        'valor'
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'valor' => 'decimal:2',
    ];

    // Relação com o Orçamento do Paciente
    public function orcamentoPaciente(): BelongsTo
    {
        return $this->belongsTo(OrcamentoPaciente::class, 'orcamento_paciente_id');
    }

    // Relação com o Procedimento — usando a coluna que REALMENTE existe
    public function procedimento(): BelongsTo
    {
        return $this->belongsTo(Procedimento::class, 'procedimento_dente_id');
    }
}