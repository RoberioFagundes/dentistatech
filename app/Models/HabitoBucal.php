<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HabitoBucal extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela (conforme está na sua migration)
    protected $table = "habito_bucals";

    protected $fillable = [
        'frequenciaDorCabeca',
        'rangeDente',
        'dor_mandibula',
        'sangramentoGengival',
        'habitos',
        'aparelho',
        'medoDentista',
        'AnsiedadeDentista',
        'ultimaVisitaDentista',
        'declaracao',
        'user_id',
        'paciente_id'
    ];

    /**
     * Retorna o Paciente dono deste histórico de hábitos bucais.
     * (Um registro de hábitos bucais PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}