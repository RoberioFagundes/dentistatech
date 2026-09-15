<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered; // <-- Adicione esta linha aqui




class FuncionarioController extends Controller
{
    public function __construct()
    {
        // Agora o método middleware() existe e funciona
        $this->middleware('auth');
    }


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

    public function edit(User $funcionario)
    {
        // Verifica se o funcionário pertence à clínica do gestor logado
        if ($funcionario->clinica_id !== auth()->user()->clinica_id) {
            abort(403, 'Acesso negado.');
        }

        return view('dentista.funcionario.formulario', compact('funcionario'));
    }

    public function update(Request $request, User $funcionario)
    {
        // Verifica se o funcionário pertence à clínica do gestor logado
        if ($funcionario->clinica_id !== auth()->user()->clinica_id) {
            abort(403, 'Acesso negado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($funcionario->id)],
            'cargo' => ['required', Rule::in(['recepcionista', 'dentista', 'auxiliar', 'gestor'])],
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('users')->ignore($funcionario->id)],
            'telefone' => 'nullable|string|max:15',
            'data_admissao' => 'nullable|date',
            'ativo' => 'boolean',
        ]);

        $funcionario->update($validated);

        return redirect()->route('funcionarios.index')
            ->with('success', "Funcionário {$funcionario->name} atualizado com sucesso!");
    }

    public function show(User $funcionario)
    {
        // Verifica se o funcionário pertence à clínica do gestor logado
        if ($funcionario->clinica_id !== auth()->user()->clinica_id) {
            abort(403, 'Acesso negado.');
        }

        return view('dentista.funcionario.show', compact('funcionario'));
    }

    public function destroy(User $funcionario)
    {
        // Verifica se o funcionário pertence à clínica do gestor logado
        if ($funcionario->clinica_id !== auth()->user()->clinica_id) {
            abort(403, 'Acesso negado.');
        }

        // AO INVÉS DE EXCLUIR, APENAS DESATIVA
        $funcionario->update(['ativo' => 0]);

        return redirect()->route('funcionarios.index')
            ->with('success', "Funcionário {$funcionario->name} desativado com sucesso! Histórico preservado.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Único DENTRO DA MESMA CLÍNICA (não impede mesmo e-mail em clínicas diferentes)
                Rule::unique('users')->where(fn($q) => $q->where('clinica_id', auth()->user()->clinica_id))
            ],
            'password' => 'required|string|min:8|confirmed',
            'cargo' => ['required', Rule::in(['recepcionista', 'dentista', 'auxiliar', 'gestor'])],
            'cpf' => [
                'nullable',
                'string',
                'max:14',
                // Único DENTRO DA MESMA CLÍNICA
                Rule::unique('users')->where(fn($q) => $q->where('clinica_id', auth()->user()->clinica_id))
            ],
            'telefone' => 'nullable|string|max:15',
            'data_admissao' => 'nullable|date',
            'ativo' => 'boolean',
        ]);

        // Vínculo AUTOMÁTICO e INQUEBRÁVEL com a clínica do gestor logado
        $funcionario = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cargo' => $validated['cargo'],
            'cpf' => $validated['cpf'],
            'telefone' => $validated['telefone'],
            'data_admissao' => $validated['data_admissao'],
            'ativo' => $validated['ativo'] ?? true,
            // FORÇA o vínculo — usuário não consegue alterar esse valor por formulário
            'clinica_id' => auth()->user()->clinica_id,
        ]);

        event(new Registered($funcionario));

        return redirect()->route('funcionarios.index')
            ->with('success', "Funcionário {$funcionario->name} cadastrado com sucesso! Um e-mail de confirmação foi enviado.");
    }
}
