<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // ✅ Importação correta da relação

class ProcedimentoDente extends Model
{
    use HasFactory;

    protected $table = "procedimento_dentes";

    protected $fillable = [
        'numero_dente',
        'procedimento_id',
        'atendimento_id',
        'observacao',
        'user_id'
    ];

    /**
     * Relacionamento: Pertence a um Atendimento
     */
    public function atendimento(): BelongsTo // ✅ Tipagem correta
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    /**
     * Relacionamento: Pertence a um Procedimento
     */
    public function procedimento(): BelongsTo // ✅ Tipagem correta
    {
        return $this->belongsTo(Procedimento::class, 'procedimento_id');
    }
}