<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Adicionado para tipar corretamente
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Atendimento extends Model
{
    use HasFactory;

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
     * Relacionamento: Atendimento pertence a um Agendamento
     */
    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }

    /**
     * Relacionamento: Atendimento tem um Orçamento do Paciente
     */
    public function orcamento(): HasOne
    {
        return $this->hasOne(OrcamentoPaciente::class, 'atendimento_id');
    }

    /**
     * Relacionamento: Atendimento tem um Prontuário
     */
    public function prontuario(): HasOne
    {
        return $this->hasOne(Prontuario::class, 'atendimento_id');
    }

    /**
     * Relacionamento: Atendimento pertence a um Paciente
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Relacionamento: Atendimento pertence a um Dentista
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    /**
     * Relacionamento: Atendimento pertence a um Usuário (se for o caso)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacionamento: Atendimento pertence a um Procedimento de Dente
     */
    public function procedimentoDente(): BelongsTo
    {
        return $this->belongsTo(ProcedimentoDente::class, 'procedimento_dente_id');
    }

    /**
     * Relacionamento: Atendimento tem vários Procedimentos (Tabela pivot: atendimento_procedimento)
     * ✅ TIPAGEM ADICIONADA PARA MAIOR SEGURANÇA
     */
    public function procedimentos(): BelongsToMany
    {
        return $this->belongsToMany(Procedimento::class, 'atendimento_procedimento');
    }
}