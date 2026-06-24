<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Agendamento extends Model
{
    protected $table = "agendamentos";
    
    protected $fillable = [
        'data_agendamento',
        'hora_agendamento',
        'paciente_id',
        'dentista_id',
        'status' // Ex: 'Pendente', 'Confirmado', 'Em Atendimento', 'Concluido', 'Faltou'
    ];

    /**
     * Retorna o Paciente que realizou este agendamento.
     * (Um agendamento PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Retorna o Dentista responsável por este agendamento.
     * (Um agendamento PERTENCE A um dentista)
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    /**
     * Retorna o Atendimento clínico gerado a partir deste agendamento.
     * (Um agendamento POSSUI UM atendimento quando o paciente comparece)
     */
    public function atendimento(): HasOne
    {
        // Se na sua migration de atendimentos a chave se chama 'agendamento_id'
        return $this->hasOne(Atendimento::class, 'agendamento_id');
    }
} 