<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    //
    protected $fillable = [
        'user_id',
        'cargo',
        'cpf',
        'telefone',
        'data_admissao',
        'ativo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
