<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Feriado extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela no plural
    protected $table = "feriados";

    protected $fillable = [
        'titulo',       // Ex: 'Natal', 'Aniversário da Cidade'
        'data',         // Tipo date (AAAA-MM-DD)
        'tipo',         // Ex: 'Nacional', 'Estadual', 'Municipal', 'Recesso'
        'user_id',
        'bloqueia_agenda' // Booleano (true/false) para saber se impede marcações
    ];

    /**
     * Opcional: Garante que o campo 'data' seja tratado como um objeto Carbon/DateTime
     * e o campo 'bloqueia_agenda' seja tratado como booleano puro no PHP.
     */
    protected $casts = [
        'data' => 'date',
        'bloqueia_agenda' => 'boolean'
    ];
}