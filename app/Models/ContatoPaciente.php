<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContatoPaciente extends Model
{
    // Define explicitamente o nome da tabela
    protected $table = "contato_pacientes";

    protected $fillable = [
        'telefone',
        'celular',
        'email',
        'paciente_id'
    ];

    /**
     * Retorna o Paciente dono deste registro de contato.
     * (Um registro de contato PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}