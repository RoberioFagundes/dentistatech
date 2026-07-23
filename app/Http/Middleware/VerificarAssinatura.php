<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarAssinatura
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Permitir acesso se for admin
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Buscar a clínica vinculada ao usuário (ajuste a relação conforme seu modelo)
        $clinica = $user->clinica; // ou Clinica::where('user_id', $user->id)->first();

        // Tratar caso a clínica não exista
        if (!$clinica) {
            auth()->logout();
            return redirect()->route('login')->with('erro', 'Clínica não encontrada para este usuário.');
        }

        // Agora $clinica está garantida como existente
        if ($clinica->assinaturaAtiva()) {
            return $next($request);
        }

        if ($clinica->onTrial()) {
            return $next($request);
        }

        return redirect()->route('planos')->with('aviso', 'Seu período de teste expirou ou sua assinatura está vencida. Assine para continuar usando o sistema.');
    }
}
