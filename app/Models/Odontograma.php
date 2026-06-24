<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Odontograma extends Model
{
    // Define explicitamente o nome da tabela (padrão em português)
    protected $table = "odontogramas";

    protected $fillable = [
        'paciente_id',
        'atendimento_id', // Opcional: para saber em qual consulta a alteração foi feita
        'numero_dente',   // Ex: 11, 12, 21, 48 (Notação FDI)
        'face_dente',     // Ex: 'Oclusal', 'Mesial', 'Distal', 'Vestibular', 'Lingual', 'Geral'
        'status_dente',   // Ex: 'Cárie', 'Restaurado', 'Tratamento de Canal', 'Ausente'
        'observacoes'
    ];

    /**
     * Retorna o Paciente dono deste dente/odontograma.
     * (Um registro de odontograma PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Retorna o Atendimento onde este dente foi modificado/diagnosticado.
     * (Um registro de odontograma pode PERTENCER A um atendimento clínico)
     */
    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }
}