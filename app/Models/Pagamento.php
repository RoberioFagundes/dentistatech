<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pagamento extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela
    protected $table = "pagamentos";
    /*
    	valor_pago	decimal(10,2)			Não	Nenhum			Muda Muda	Eliminar Eliminar	
	3	forma_pagamento	varchar(45)	utf8mb4_unicode_ci		Não	Nenhum			Muda Muda	Eliminar Eliminar	
	4	status_pagamento	enum('PENDENTE', 'PAGO', 'CANCELADO', 'ESTORNADO')	utf8mb4_unicode_ci		Não	PENDENTE			Muda Muda	Eliminar Eliminar	
	5	data_pagamento	timestamp			Sim	NULL			Muda Muda	Eliminar Eliminar	
	6	observacao	varchar(255)	utf8mb4_unicode_ci		Sim	NULL			Muda Muda	Eliminar Eliminar	
	7	orcamento_paciente_id Índice	bigint		UNSIGNED	Não	Nenhum			Muda Muda	Eliminar Eliminar	
	8	user_id Índice	bigint		UNSIGNED	Sim	NULL			Muda Muda	Eliminar Eliminar	
	9	created_at	timestamp			Sim	NULL			Muda Muda	Eliminar Eliminar	
	10	updated_at
    */

    protected $fillable = [
        'paciente_id',
        'orcamento_paciente_id',       // ID do orçamento pai (OrcamentoPaciente)
        'forma_pagamento', // ID da modalidade (Pix, Crédito, etc.)
        'valor_pago',
        'user_id',
        'data_pagamento',
        'status_pagamento',   // Ex: 'Pendente', 'Pago', 'Estornado'
        'observacao'
    ];

    /**
     * Garante a conversão de tipos de dados para o PHP.
     */
    protected $casts = [
        'valor_pago' => 'float',
        'data_pagamento' => 'datetime', // Inclui data e hora do recebimento
    ];

    /**
     * Retorna o Paciente que realizou este pagamento.
     * (Um pagamento PERTENCE A um paciente)
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Retorna o Orçamento/Plano de Tratamento vinculado a este pagamento.
     * (Um pagamento PERTENCE A um orçamento de paciente)
     */
    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(OrcamentoPaciente::class, 'orcamento_id');
    }

     // ✅ RELACIONAMENTO CORRETO: Um pagamento pertence a um orçamento
    public function orcamentoPaciente(): BelongsTo
    {
        return $this->belongsTo(OrcamentoPaciente::class, 'orcamento_paciente_id');
    }

    // ✅ RELACIONAMENTO COM USUÁRIO: Um pagamento pertence a um usuário
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Retorna a Forma de Pagamento escolhida para esta transação.
     * (Um pagamento PERTENCE A uma forma de pagamento)
     */
    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(FormaPagamento::class, 'forma_pagamento_id');
    }
}