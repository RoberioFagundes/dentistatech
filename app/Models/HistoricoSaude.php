<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HistoricoSaude extends Model
{
    use HasFactory;
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
        'user_id',
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