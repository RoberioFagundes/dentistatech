<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContatoDentista extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela
    protected $table = "contato_dentistas";

    protected $fillable = [
        'telefone',
        'whatsapp',
        'celular',
        'email',
        'user_id',
        'dentista_id'
    ];

    /**
     * Retorna o Dentista ao qual este registro de contato pertence.
     * (Um registro de contato PERTENCE A um dentista)
     */
    public function dentista(): BelongsTo
    {
        return $this->belongsTo(Dentista::class, 'dentista_id');
    }
}