<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hospitalizado extends Model
{
    // Define explicitamente o nome da tabela (conforme o padrão em português da sua migration)
    protected $table = "hospitalizados";

    protected $fillable = [
        'motivo',          // Ex: 'Cirurgia cardíaca', 'Tratamento de infecção'
        'data_internacao', // Data da entrada no hospital
        'data_alta',       // Data da saída do hospital
        'observacoes',     // Notas adicionais sobre o ocorrido
        'user_id',
        'paciente_id'      // Chave estrangeira que aponta para o paciente
    ];

    /**
     * Define a conversão nativa de colunas de data para objetos Carbon/DateTime.
     */
    protected $casts = [
        'data_internacao' => 'date',
        'data_alta' => 'date',
    ];

    /**
     * Retorna o Paciente ao qual este histórico de hospitalização pertence.
     * (Um registro de hospitalização PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}