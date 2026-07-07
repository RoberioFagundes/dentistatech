<?php

namespace App\Http\Controllers;
use App\Models\Agendamento;
use App\Models\Atendimento;
use App\Models\Procedimento;
use App\Models\Paciente;
use App\Models\Dentista;
use App\Models\Pagamento;
use App\Models\OrcamentoPaciente;
use App\Models\Orcamentoitem;

use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        // Busca apenas os registros do usuário logado
        // O 'latest()' ordena pelos mais recentes
        $financeiros = \App\Models\Pagamento::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('dentista.financeiro.index', compact('financeiros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('dentista.financeiro.formulario');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id) { 
    $financeiro = Financeiro::findOrFail($id);
        return view('dentista.financeiro.formulario', compact('financeiro')); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
