<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DentistaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = $request->user();

        // Verifica se o campo 'tipo' do usuário logado é exatamente 'dentista'
        if ($user->tipo === 'administrador') {
            return $next($request);
        }

        abort(403, 'Esta ação não é autorizada. Seu tipo atual é: ' . $user->tipo);
    }
}