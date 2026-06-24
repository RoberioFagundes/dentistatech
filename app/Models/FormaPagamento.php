<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormaPagamento extends Model
{
    // Define explicitamente o nome da tabela
    protected $table = "forma_pagamentos";

    protected $fillable = [
        'nome',            // Ex: 'Pix', 'Cartão de Crédito', 'Dinheiro'
        'taxa_desconto',   // Ex: 2.50 (Caso haja taxa da maquininha de cartão)
        'ativo'            // Booleano para ativar/desativar a forma de pagamento na recepção
    ];

    /**
     * Garante que o campo 'ativo' seja tratado como booleano puro no PHP
     * e a taxa seja tratada como float/decimal.
     */
    protected $casts = [
        'ativo' => 'boolean',
        'taxa_desconto' => 'float'
    ];

    /**
     * Retorna todos os pagamentos que foram realizados utilizando esta forma de pagamento.
     * (Uma forma de pagamento POSSUI MUITOS pagamentos associados)
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'forma_pagamento_id');
    }
}