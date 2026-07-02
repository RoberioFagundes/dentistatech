<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnderecoDentistaAtendimento extends Model
{
    // Define explicitamente o nome da tabela no plural conforme o padrão do Laravel
    protected $table = "endereco_dentista_atendimentos";

   protected $fillable = ['dentista_id', 'cep', 'pais', 'estado', 'cidade', 'bairro', 'rua', 'numero'];

    /**
     * Retorna o Dentista vinculado a este endereço de atendimento.
     * (Este registro PERTENCE A um dentista)
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    /**
     * Retorna o Atendimento vinculado a este endereço.
     * (Este registro PERTENCE A um atendimento)
     */
    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }
}