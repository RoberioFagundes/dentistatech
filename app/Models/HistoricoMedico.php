<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoMedico extends Model
{
    //
    protected $table = "historico_medicos";
    protected $fillable = [
        'alergias',
        'medicacaoEmUso',
        'condicaoCronica',
        'historicoCirurgico',
        'observacaoMedicaGeral',
        'paciente_id'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}

 