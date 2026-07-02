<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Atendimento extends Model
{
    protected $table = "atendimentos";

    protected $fillable = [
        'agendamento_id',
        'status_atendimento', // Ex: 'Em Andamento', 'Finalizado', 'Cancelado'
        'observacoes_iniciais',
        'user_id'
    ];

    /**
     * Retorna o Agendamento (reserva de horário) que deu origem a este atendimento.
     * (Um atendimento PERTENCE A um agendamento)
     */
    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }

    /**
     * Retorna os Orçamentos gerados a partir deste atendimento clínico.
     * (Um atendimento pode gerar UM ou MAIS orçamentos para o paciente)
     */
    public function orcamentos(): HasMany
    {
        return $this->hasMany(OrcamentoPaciente::class, 'atendimento_id');
    }

    /**
     * Retorna o Prontuário clínico consolidado deste atendimento.
     * (Um atendimento POSSUI UM registro de prontuário/evolução histórica)
     */
    public function prontuario(): HasOne
    {
        return $this->hasOne(Prontuario::class, 'atendimento_id');
    }
}