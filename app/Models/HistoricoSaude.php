<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoSaude extends Model
{
    // Define explicitamente o nome da tabela (padrão em português conforme sua migration)
    protected $table = "historico_saudes";

    protected $fillable = [
        'tratamento_medico',
        'medicamento_uso_continuo',
        'gravidez',
        'alergias',
        'problemas_cardiacos',
        'diabetes',
        'pressao_arterial',
        'fumante',
        'observacoes_medicas',
        'paciente_id'
    ];

    /**
     * Retorna o Paciente dono deste histórico de saúde.
     * (Um registro de histórico de saúde PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}