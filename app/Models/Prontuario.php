<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prontuario extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela no plural
    protected $table = "prontuarios";

    protected $fillable = [
        'paciente_id',
        'dentista_id',
        'atendimento_id', // Opcional: Viniciona a evolução à consulta específica do dia
        'descricao',      // Texto contendo o relato clínico (ex: "Realizada exodontia do dente 18...")
        'prescricao',     // Opcional: Medicamentos receitados na consulta
        'user_id',
        'data_registro'   // Data e hora em que a evolução foi registrada/assinada
    ];

    /**
     * Define a conversão de tipos nativos para o PHP.
     */
    protected $casts = [
        'data_registro' => 'datetime',
    ];

    /**
     * Retorna o Paciente que recebeu o atendimento descrito neste prontuário.
     * (Uma evolução de prontuário PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Retorna o Dentista responsável por realizar o procedimento e preencher o prontuário.
     * (Uma evolução de prontuário PERTENCE A um dentista)
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    /**
     * Retorna o Atendimento/Consulta que gerou este registro.
     * (Uma evolução de prontuário pode PERTENCER A um atendimento clínico)
     */
    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }
}