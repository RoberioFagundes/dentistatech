<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered; // <-- Adicione esta linha aqui



class FuncionarioController extends Controller
{
   

    // Lista de funcionários da clínica
    public function index()
    {
        $funcionarios = User::where('clinica_id', auth()->user()->clinica_id)->get();
        return view('dentista.funcionario.index', compact('funcionarios'));
    }

    // Formulário de cadastro
    public function create()
    {
        return view('dentista.funcionario.formulario');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'cargo' => ['required', Rule::in(['recepcionista', 'dentista', 'auxiliar', 'gestor'])],
            'cpf' => 'nullable|string|max:14|unique:users',
            'telefone' => 'nullable|string|max:15',
            'data_admissao' => 'nullable|date',
            'ativo' => 'boolean',
        ]);

        // Vínculo AUTOMÁTICO com a clínica do gestor logado
        $funcionario = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cargo' => $validated['cargo'],
            'cpf' => $validated['cpf'],
            'telefone' => $validated['telefone'],
            'data_admissao' => $validated['data_admissao'],
            'ativo' => $validated['ativo'] ?? true,
            'clinica_id' => auth()->user()->clinica_id,
        ]);

        // Dispara o evento de registro para enviar o e-mail de confirmação
        event(new Registered($funcionario));

        return redirect()->route('funcionarios.index')
            ->with('success', "Funcionário {$funcionario->name} cadastrado com sucesso! Um e-mail de confirmação foi enviado.");
    }
}
