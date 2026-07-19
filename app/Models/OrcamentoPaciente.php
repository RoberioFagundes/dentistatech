<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrcamentoPaciente extends Model
{
    use HasFactory;

    protected $table = "orcamento_pacientes";

    protected $fillable = [
        'validade',
        'valor',
        'total_orcamento',
        'desconto',
        'valor_liquido',
        'saldo_restante',
        'paciente_id',
        'atendimento_id',
        'procedimento_id',
        'procedimento_dentes_id',
        'user_id',
        'dentista_id',
    ];

    protected $casts = [
        'validade' => 'date',
        'total_orcamento' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_liquido' => 'decimal:2',
        'saldo_restante' => 'decimal:2', // ✅ Adicionei para manter precisão
    ];

    protected static function booted()
    {
        // ✅ Corrigido: usa os campos que existem no banco!
        static::creating(function ($orcamento) {
            $orcamento->saldo_restante = $orcamento->valor_liquido ?? $orcamento->total_orcamento ?? 0;
        });
    }

    // Relacionamentos
    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'orcamento_paciente_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(OrcamentoItem::class, 'orcamento_id');
    }
}
