<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoBucal extends Model
{
    protected $table = "historico_bucals";
    protected $fillable = [
        'ultima_consulta',
        'motivo_consulta_atual',
        'sangramento_gengival',
        'sensibilidade_dental',
        'uso_proteses_aparelho',
        'tipo_aparelho_ou_protese',
        'tratamento_bucal_anterior',
        'observacoes_bucal_adicionais',
        'paciente_id'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}
