<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FilaAtendimento extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela
    protected $table = "fila_atendimentos";

    protected $fillable = [
        'agendamento_id',
        'status_fila',
        'prioridade',
        'chegada_em',
        'chamado_em',
        'user_id'
    
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