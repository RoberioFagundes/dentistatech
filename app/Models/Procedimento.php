<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Procedimento extends Model
{
    // Define explicitamente o nome da tabela no plural
    protected $table = "procedimentos";

    protected $fillable = [
        'nome',             // Ex: 'Restauração de Resina', 'Tratamento de Canal'
        'descricao',   // Opcional: Código interno ou TUSS (Ex: 85100012)
        'valor',       // Valor padrão sugerido do procedimento
        'user_id',
        'tempo_estimado',   // Tempo estimado em minutos (Ex: 45) para auxiliar a agenda
        'ativo'             // Booleano para ativar/desativar o procedimento no catálogo
    ];

    /**
     * Define a conversão de tipos para garantir a consistência dos dados.
     */
    protected $casts = [
        'valor' => 'float',
        'tempo_estimado' => 'integer',
        'ativo' => 'boolean'
    ];

    /**
     * Retorna todos os itens de orçamento que utilizam este procedimento.
     * (Um procedimento do catálogo pode estar presente em MUITOS itens de orçamentos)
     */
    public function itensOrcamento(): HasMany
    {
        return $this->hasMany(OrcamentoItem::class, 'procedimento_id');
    }
}