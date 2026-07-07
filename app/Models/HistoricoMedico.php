<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HistoricoMedico extends Model
{
    use HasFactory;
    //
    protected $table = "historico_medicos";
    protected $fillable = [
        'alergias',
        'medicacaoEmUso',
        'condicaoCronica',
        'historicoCirurgico',
        'observacaoMedicaGeral',
        'user_id',
        'paciente_id'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}

 