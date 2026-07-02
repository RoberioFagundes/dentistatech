<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilaAtendimento extends Model
{
    // Define explicitamente o nome da tabela
    protected $table = "fila_atendimentos";

    protected $fillable = [
        'paciente_id',
        'dentista_id',
        'agendamento_id',
        'user_id',
        'posicao',           // Número da senha ou ordem numérica na fila
        'status_fila',       // Ex: 'Aguardando', 'Chamado', 'Em Atendimento', 'Ausente'
        'hora_chegada'
    ];

    /**
     * Retorna o Paciente que está aguardando na fila.
     * (Um registro da fila PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Retorna o Dentista que chamará este paciente.
     * (Um registro da fila PERTENCE A um dentista específico)
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    /**
     * Retorna o Agendamento de origem, se houver.
     * (Um registro da fila pode ESTAR VINCULADO A um agendamento)
     */
    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }
}