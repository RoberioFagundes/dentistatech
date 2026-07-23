<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinica extends Model
{
    use HasFactory;

    // ✅ TODOS os campos liberados para salvar
    protected $fillable = [
        'razao_social',
        'cnpj',
        'telefone',
        'endereco',
        'trial_ends_at',
        'trial_used',
        'status_assinatura',
        'asaas_customer',
        'asaas_subscription',
    ];

    // ✅ Converte datas automaticamente
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'trial_used'    => 'boolean',
    ];

    // Verifica se está no período de teste (incluindo quem ainda não iniciou a contagem)
    public function onTrial()
    {
        return $this->status_assinatura === 'trial'
            && (
                $this->trial_used == 0 // Ainda não começou → liberado
                || ($this->trial_used == 1 && $this->trial_ends_at && $this->trial_ends_at->isFuture()) // Já começou e data é futura
            );
    }

    // Método que INICIA A CONTAGEM dos 15 dias
    public function iniciarTeste()
    {
        $this->update([
            'trial_ends_at' => now()->addDays(15),
            'trial_used'    => 1, // ✅ Agora sim começa a contar
            'status_assinatura' => 'trial',
        ]);
    }

    // ✅ Verifica assinatura ativa
    public function assinaturaAtiva()
    {
        return $this->status_assinatura === 'ativa';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
