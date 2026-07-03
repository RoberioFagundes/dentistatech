<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcedimentoDente extends Model
{
    protected $table = "procedimento_dentes";
    
    protected $fillable = [
        'numero_dente', 
        'procedimento_id', 
        'atendimento_id', 
        'observacao',
        'user_id'
    ];

    // Relacionamento: Cada registro de procedimento_dente pertence a um atendimento
    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    // Relacionamento: Cada registro de procedimento_dente pertence a um procedimento (tabela de tipos de procedimento)
    public function procedimento()
    {
        return $this->belongsTo(Procedimento::class, 'procedimento_id');
    }
    
}
