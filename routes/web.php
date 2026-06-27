<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Página Inicial
Route::get('/', function () {
    return view('welcome');
});

// Dashboard Centralizado (Com redirecionamento por tipo de usuário)
Route::get('/dashboard', function () {
    // O middleware 'auth' já garante que o usuário está logado, 
    // então podemos acessar o Auth::user() diretamente com segurança.
    return match (Auth::user()->tipo) {
        'recepcionista' => view('recepcionista.dashboardRecepcionista'),
        'dentista'      => view('dentista.dashboardDentista'),
        'administrador' => view('administrador.dashboardAdministrador'),
        'paciente'      => view('paciente.dashboardPaciente'),
        default         => abort(403, 'Tipo de usuário não autorizado.'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas Protegidas de Perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';