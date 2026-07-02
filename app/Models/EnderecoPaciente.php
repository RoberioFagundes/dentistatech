<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnderecoPaciente extends Model
{
    // Define explicitamente o nome da tabela (padrão em português)
    protected $table = "endereco_pacientes";

    protected $fillable = [
        'cep',
        'pais',
        'estado',
        'cidade',
        'bairro',
        'rua',
        'numero',
        'user_id',
        'paciente_id'
    ];

    /**
     * Retorna o Paciente ao qual este endereço pertence.
     * (Um endereço PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}