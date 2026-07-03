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
        'queixa_principal',
        'diagnostico',
        'tratamento_executado',
        'observacoes_clinicas',
         'paciente_id', 
        'dentista_id', 
        'agendamento_id', 
        'procedimento_dente_id',
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

    /**
     * Retorna o Paciente associado a este atendimento.
     * (Um atendimento PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function procedimentoDente(): BelongsTo
    {
        return $this->belongsTo(ProcedimentoDente::class, 'procedimento_dente_id');
    }

    public function procedimento_dentes()
    {
        return $this->hasMany(ProcedimentoDente::class, 'atendimento_id');
    }
}