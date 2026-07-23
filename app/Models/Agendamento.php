<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = "agendamentos";

    // ✅ Todos os campos que existem na tabela, liberados para edição
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

    // ✅ Conversão automática de tipos
    protected $casts = [
        'data' => 'date:Y-m-d',
        // 'hora_inicio' => 'datetime:H:i',
        // 'hora_fim' => 'datetime:H:i',
    ];

    // ✅ Relacionamentos existentes e confirmados
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    public function procedimento(): BelongsTo
    {
        return $this->belongsTo(Procedimento::class, 'procedimento_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ Relacionamento com remarcações (histórico)
    public function remarcacoes()
    {
        return $this->hasMany(Remarcacao::class, 'agendamento_id');
    }
}
