<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Remarcacao extends Model
{
    use HasFactory;

    protected $table = 'remarcacoes';

    protected $fillable = [
        'agendamento_id',
        'data_anterior',
        'data_nova',
        'motivo',
        'user_id',
    ];

    protected $casts = [
        'data_anterior' => 'datetime',
        'data_nova' => 'datetime',
    ];

    // Relacionamento com o agendamento original
    public function agendamento(): BelongsTo
    {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }

    // Relacionamento com o usuário que fez a remarcação
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
