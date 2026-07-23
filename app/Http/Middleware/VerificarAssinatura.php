<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarAssinatura
{
    public function handle(Request $request, Closure $next)
    {
        // Se não estiver logado, segue normal
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $clinica = $user->clinica;

        // ✅ Permite acesso a essas páginas SEMPRE
        $rotasPermitidas = ['planos', 'assinatura.criar', 'login', 'register', 'home', 'logout'];
        if (in_array($request->route()->getName(), $rotasPermitidas)) {
            return $next($request);
        }

        // ✅ Se for administrador (se tiver) libera tudo
        if ($user->role === 'admin') {
            return $next($request);
        }

        // ✅ Se assinatura ativa → segue normalmente
        if ($clinica->assinaturaAtiva()) {
            return $next($request);
        }

        // ✅ Se ainda está no teste → segue normalmente
        if ($clinica->onTrial()) {
            return $next($request);
        }

        // ⛔ Se chegou aqui: teste acabou / assinatura vencida
        return redirect()->route('planos')->with('aviso', 'Seu período de teste expirou ou sua assinatura está vencida. Assine para continuar usando o sistema.');
    }
}