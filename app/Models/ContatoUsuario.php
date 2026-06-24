<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContatoUsuario extends Model
{
    // Define explicitamente o nome da tabela
    protected $table = "contato_usuarios";

    protected $fillable = [
        'telefone',
        'celular',
        'email',
        'usuario_id' // Altere para 'user_id' se sua tabela pai for 'users'
    ];

    /**
     * Retorna o Usuário/Colaborador dono deste registro de contato.
     * (Um registro de contato PERTENCE A um usuário)
     */
    public function usuario(): BelongsTo
    {
        // Se a sua tabela se chama 'usuarios' e o modelo 'Usuario':
        return $this->belongsTo(Usuario::class, 'usuario_id');
        
        // Se você manteve o padrão nativo do Laravel ('users' e modelo 'User'):
        // return $this->belongsTo(User::class, 'user_id');
    }
}