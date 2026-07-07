<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- ADICIONE ESTA IMPORTAÇÃO
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EnderecoUsuario extends Model
{
    use HasFactory;
    // Define explicitamente o nome da tabela
    protected $table = "endereco_usuarios";

    protected $fillable = [
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'user_id',
        'estado',
        'usuario_id' // Altere para 'user_id' se sua tabela pai for 'users'
    ];

    /**
     * Retorna o Usuário/Colaborador dono deste endereço.
     * (Um endereço de usuário PERTENCE A um usuário)
     */
    public function usuario(): BelongsTo
    {
        // Se a sua tabela se chama 'usuarios' e o modelo 'Usuario':
        return $this->belongsTo(Usuario::class, 'usuario_id');
        
        // Se você utilizou o padrão nativo do Laravel ('users' e modelo 'User'):
        // return $this->belongsTo(User::class, 'user_id');
    }
}