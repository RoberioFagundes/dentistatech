<?php

namespace App\Models;

use App\Models\Agendamento;
use App\Models\EnderecoDentistaAtendimento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dentista extends Model
{
    use HasFactory; // <--- AGORA O MÉTODO factory() FUNCIONARÁ

    // Define explicitamente o nome da tabela
    protected $table = "dentistas";

    protected $fillable = ['user_id', 'nome', 'sobrenome', 'cro', 'cro_uf', 'especialidade'];

    /**
     * Retorna os registros de contato deste profissional.
     */
    // ✅ Agora retorna UM registro apenas
    public function contatos(): HasOne
    {
        return $this->hasOne(ContatoDentista::class, 'dentista_id');
    }

    /**
     * Retorna o endereço da clínica/consultório do dentista.
     */
    public function endereco(): HasOne
    {
        return $this->hasOne(EnderecoDentistaAtendimento::class, 'dentista_id');
    }

    /**
     * Retorna todos os agendamentos marcados para este dentista.
     */
    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'dentista_id');
    }

    /**
     * Retorna todos os prontuários clínicos preenchidos por este dentista.
     */
    public function prontuarios(): HasMany
    {
        return $this->hasMany(Prontuario::class, 'dentista_id');
    }
}