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

        if (!$user) {
            return redirect()->route('login');
        }

        // ✅ Administradores e Dentistas podem ter livre acesso ou regras próprias
        // Ajuste aqui caso dentistas precisem validar assinatura
        // if (in_array($user->tipo, ['administrador', 'dentista', 'recepcionista'])) {
        //     return $next($request);
        // }

        // Buscar a clínica vinculada ao usuário (para pacientes ou outros perfis)
        $clinica = $user->clinica ?? null;

        if (!$clinica) {
            return redirect()->route('login')->with('erro', 'Clínica não encontrada para este usuário.');
        }

        // Verifica assinatura ativa ou período de testes
        if ($clinica->assinaturaAtiva() || $clinica->onTrial()) {
            return $next($request);
        }

        return redirect()->route('planos')->with('aviso', 'Seu período de teste expirou ou sua assinatura está vencida. Assine para continuar usando o sistema.');
    }
}