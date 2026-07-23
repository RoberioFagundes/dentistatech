<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Clinica;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validação mantida exatamente como você fez
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'razao_social' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:255', 'unique:clinicas,cnpj'],
            'telefone' => ['required', 'string', 'max:255'],
            'endereco' => ['required', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($dados) {

            // Cria a clínica só com os dados básicos
            $clinica = Clinica::create([
                'razao_social' => $dados['razao_social'],
                'cnpj'         => $dados['cnpj'],
                'telefone'     => $dados['telefone'],
                'endereco'     => $dados['endereco'],
            ]);

            // ✅ Chama o método que já está no Model para iniciar o teste
            $clinica->iniciarTeste();

            // Cria o usuário normalmente
            $user = User::create([
                'name'       => $dados['name'],
                'email'      => $dados['email'],
                'password'   => Hash::make($dados['password']),
                'clinica_id' => $clinica->id,
                'role'       => 'dentista',
            ]);

            event(new Registered($user));
            return $user;
        });

        Auth::login($user);
        return redirect()->route('dashboard')->with('sucesso', 'Cadastro realizado! Seu teste de 15 dias já está ativo.');
    }
}
