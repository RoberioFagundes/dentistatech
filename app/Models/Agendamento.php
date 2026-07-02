<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    protected $table = "agendamentos";

    protected $fillable = [
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        'observacao',
        'dentista_id',
        'paciente_id',
        'atendimento_id',
        'procedimento_id',
        'user_id',
    ];

    // Garante que o Laravel entenda os campos de hora/data corretamente
    protected $casts = [
        'data' => 'date',
    ];

    /**
     * Retorna o Paciente que realizou este agendamento.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Retorna o Dentista responsável por este agendamento.
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    /**
     * Retorna o Procedimento/Atendimento vinculado a este agendamento.
     * AJUSTADO: Como o agendamento possui a coluna 'atendimento_id', ele belongsTo Atendimento.
     */
    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }


public function procedimento(){
    
    return $this->belongsTo(Procedimento::class, 'procedimento_id');
}

// existe essa?

}