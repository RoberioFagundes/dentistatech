<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FilaAgendamento extends Model
{
    use HasFactory;
    //
     protected $guarded = [];

    public function paciente(){
      
        return $this->belongsTo(Paciente::class,'paciente_id');

    }

    public function agendamento(){

        return $this->belongsTo(Agendamento::class,'agendamento_id');

    }
}
