<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContatoPaciente extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela
    protected $table = "contato_pacientes";

    protected $fillable = [
        'telefone',
        'whatsapp',
        'celular',
        'email',
        'user_id',
        'paciente_id'
    ];

    /**
     * Retorna o Paciente dono deste registro de contato.
     * (Um registro de contato PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id', 'id');
    }
}