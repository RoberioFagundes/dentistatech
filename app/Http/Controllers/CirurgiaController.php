<?php

namespace App\Http\Controllers;

use App\Models\Cirurgia;
use App\Models\Paciente;
use App\Models\Dentista;
use Illuminate\Http\Request;

class CirurgiaController extends Controller
{
    /**
     * Exibe uma listagem das cirurgias.
     */
    public function index(Request $request)
    {
        // Cria uma query trazendo as relações principais pré-carregadas (evita gargalo N+1)
        $query = Cirurgia::with(['paciente', 'dentista']);

        // Filtro por Paciente
        if ($request->has('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        // Filtro por Status (ex: 'Pós-Operatório', 'Agendada')
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $cirurgias = $query->orderBy('data_hora', 'desc')->paginate(10);

        return response()->json($cirurgias);
        // Se usar Blade: return view('cirurgias.index', compact('cirurgias'));
    }

    /**
     * Exibe o formulário de criação (Auxilia trazendo os seletores necessários).
     */
    public function create()
    {
        $pacientes = Paciente::where('status', 'Ativo')->orderBy('nome')->get();
        $dentistas = Dentista::where('status', 'Ativo')->orderBy('nome')->get();

        return response()->json(compact('pacientes', 'dentistas'));
    }

    /**
     * Salva uma nova cirurgia no banco de dados.
     */
    public function store(Request $request)
    {
        // Validação rigorosa dos dados de entrada
        $dadosValidados = $request->validate([
            'paciente_id'       => 'required|exists:pacientes,id',
            'dentista_id'       => 'required|exists:dentistas,id',
            'procedimento_id'   => 'nullable|exists:procedimentos,id', // Se houver vínculo com catálogo de serviços
            'tipo_cirurgia'     => 'required|string|max:150', // Ex: 'Implante Dentário', 'Exodontia de Siso'
            'data_hora'         => 'required|date|after:now',
            'sala_bloco'        => 'nullable|string|max:50',  // Sala cirúrgica específica se a clínica possuir
            'status'            => 'required|string|in:Agendada,Realizada,Cancelada,Pós-Operatório',
            'observacoes_pre'   => 'nullable|string',          // Recomendações pré-operatórias
        ]);

        $cirurgia = Cirurgia::create($dadosValidados);

        return response()->json([
            'message' => 'Cirurgia agendada com sucesso no sistema!',
            'data' => $cirurgia
        ], 201);
    }

    /**
     * Exibe os detalhes de uma cirurgia específica.
     */
    public function show(Cirurgia $cirurgia)
    {
        $cirurgia->load(['paciente', 'dentista']);
        return response()->json($cirurgia);
    }

    /**
     * Exibe o formulário de edição pré-carregando os dados.
     */
    public function edit(Cirurgia $cirurgia)
    {
        $pacientes = Paciente::where('status', 'Ativo')->orderBy('nome')->get();
        $dentistas = Dentista::where('status', 'Ativo')->orderBy('nome')->get();

        return response()->json(compact('cirurgia', 'pacientes', 'dentistas'));
    }

    /**
     * Atualiza os dados ou o Status da cirurgia (Ex: Mudar para 'Realizada' e adicionar notas pós-operatórias).
     */
    public function update(Request $request, Cirurgia $cirurgia)
    {
        $dadosValidados = $request->validate([
            'paciente_id'       => 'required|exists:pacientes,id',
            'dentista_id'       => 'required|exists:dentistas,id',
            'procedimento_id'   => 'nullable|exists:procedimentos,id',
            'tipo_cirurgia'     => 'required|string|max:150',
            'data_hora'         => 'required|date',
            'sala_bloco'        => 'nullable|string|max:50',
            'status'            => 'required|string|in:Agendada,Realizada,Cancelada,Pós-Operatório',
            'observacoes_pre'   => 'nullable|string',
            'observacoes_pos'   => 'nullable|string',          // Relato de como foi o procedimento
            'cuidados_casa'     => 'nullable|string',          // Receita ou cuidados passados ao paciente
        ]);

        $cirurgia->update($dadosValidados);

        return response()->json([
            'message' => 'Registro de cirurgia atualizado com sucesso!',
            'data' => $cirurgia
        ]);
    }

    /**
     * Cancela ou remove o registro de cirurgia.
     */
    public function destroy(Cirurgia $cirurgia)
    {
        // Evita excluir cirurgias que já foram efetuadas por questões de histórico legal do paciente
        if (in_array($cirurgia->status, ['Realizada', 'Pós-Operatório'])) {
            return response()->json([
                'error' => 'Não é permitido deletar uma cirurgia já realizada. Altere o status se necessário.'
            ], 422);
        }

        // Deleção física ou lógica (Se preferir lógica, mude para status 'Cancelada')
        $cirurgia->delete();

        return response()->json([
            'message' => 'Agendamento de cirurgia excluído com sucesso.'
        ]);
    }
}