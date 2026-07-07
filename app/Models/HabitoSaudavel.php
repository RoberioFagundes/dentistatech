<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HabitoSaudavel extends Model
{
    use HasFactory;
    //
    protected $table = "habito_saudavels";
    protected $fillable = [
        'frequencia_escovacao',
        'uso_fio_dental',
        'habito_fumo',
        'atividade_fisica',
        'qualidade_sono',
        'observacoes_estilo_vida',
        'user_id',
        'paciente_id'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}
