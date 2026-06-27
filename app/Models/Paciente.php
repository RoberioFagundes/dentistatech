<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paciente extends Model
{
    // Define explicitamente o nome da tabela (padrão em português)
    protected $table = "pacientes";

    protected $fillable = [
        'nome',
        'sobrenome',
        'cpf',
        'rg',
        'sexo',
        'data_nascimento',
        'dentista_id'
        // 'status' // Ex: 'Ativo', 'Inativo'
    ];

    /**
     * Garante que campos de data sejam tratados como objetos Carbon no PHP.
     */
    protected $casts = [
        'data_nascimento' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos de Cadastro e Localização (1 para 1 / 1 para Muitos)
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna o endereço residencial do paciente.
     */
    public function endereco(): HasOne
    {
        return $this->hasOne(EnderecoPaciente::class, 'paciente_id');
    }

    /**
     * Retorna os registros de contato do paciente (Telefone, celular, e-mail).
     */
    public function contatos(): HasMany
    {
        return $this->hasMany(ContatoPaciente::class, 'paciente_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos Clínicos e Anamnese (Fichas de Saúde)
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna a ficha de Hábitos Bucais (Bruxismo, fumo, etc.).
     */
    public function habitoBucal(): HasOne
    {
        return $this->hasOne(HabitoBucal::class, 'paciente_id');
    }

    /**
     * Retorna o Histórico de Saúde geral (Alergias, diabetes, hipertensão).
     */
    public function historicoSaude(): HasOne
    {
        return $this->hasOne(HistoricoSaude::class, 'paciente_id');
    }

    /**
     * Retorna o histórico de internações/hospitalizações do paciente.
     */
    public function hospitalizacoes(): HasMany
    {
        return $this->hasMany(Hospitalizado::class, 'paciente_id');
    }

    /**
     * Retorna o mapa do Odontograma (marcações de dentes tratados ou com cárie).
     */
    public function odontograma(): HasMany
    {
        return $this->hasMany(Odontograma::class, 'paciente_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos de Agenda e Comercial/Financeiro
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna todas as consultas e agendamentos deste paciente.
     */
    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class, 'paciente_id');
    }

    /**
     * Retorna todos os orçamentos e planos de tratamento gerados para o paciente.
     */
    public function orcamentos(): HasMany
    {
        return $this->hasMany(OrcamentoPaciente::class, 'paciente_id');
    }
}