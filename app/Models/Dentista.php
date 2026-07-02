<?php

namespace App\Models;
use App\Models\Agendamento;
use App\Models\EnderecoDentistaAtendimento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dentista extends Model
{
    // Define explicitamente o nome da tabela caso o Laravel busque pelo plural padrão em inglês
    protected $table = "dentistas";

    protected $fillable = ['user_id', 'nome', 'sobrenome', 'cro', 'cro_uf', 'especialidade'];

    /**
     * Retorna os registros de contato deste profissional.
     * (Um dentista possui vários contatos/telefones cadastrados)
     */
    public function contatos(): HasMany
    {
        return $this->hasMany(ContatoDentista::class, 'dentista_id');
    }

    /**
     * Retorna o endereço da clínica/consultório do dentista (caso utilize essa tabela).
     * (Um dentista possui um endereço atrelado)
     */
    public function endereco(): HasOne
    {
        return $this->hasOne(EnderecoDentistaAtendimento::class, 'dentista_id');
    }

    /**
     * Retorna todos os agendamentos marcados para este dentista.
     * (Um dentista possui muitos agendamentos na sua agenda)
     */
    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'dentista_id');
    }

    /**
     * Retorna todos os prontuários clínicos preenchidos por este dentista.
     * (Um dentista preenche/possui muitos prontuários)
     */
    public function prontuarios(): HasMany
    {
        return $this->hasMany(Prontuario::class, 'dentista_id');
    }
}