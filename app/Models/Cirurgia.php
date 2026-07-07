<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cirurgia extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela caso o Laravel busque pelo plural padrão em inglês
    protected $table = "cirurgias";

    protected $fillable = [
        'tipo_cirurgia',
        'ano',
        'complicacoes',
        'observacoes',
        'paciente_id', // Se na sua tabela você mudou para o padrão paciente_id
        'user_id', // Se na sua tabela você mudou para o padrão user_id
    ];

    /**
     * Retorna o Paciente ao qual este registro de cirurgia pertence.
     * (Uma cirurgia pregressa PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}