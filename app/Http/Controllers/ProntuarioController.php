<?php

namespace App\Http\Controllers;

use App\Models\Prontuario;
use App\Models\Paciente;
use Illuminate\Http\Request;

class ProntuarioController extends Controller
{
    /**
     * Exibe a linha do tempo (timeline) do prontuário de um paciente específico.
     */
    public function index(Request $request)
    {
        // Torna obrigatório o envio do ID do paciente para listar sua evolução clínica
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id'
        ]);

        // Carrega o histórico de evolução clínica com dados do dentista que realizou o procedimento
        $historico = Prontuario::with('dentista')
            ->where('paciente_id', $request->paciente_id)
            ->orderBy('data_registro', 'desc')
            ->get();

        return response()->json($historico);
        // Se usar Blade: return view('pacientes.prontuario', compact('historico'));
    }

    /**
     * Auxilia no carregamento de dados estruturantes para criação de uma evolução manual.
     */
    public function create()
    {
        // Geralmente os prontuários são criados atrelados ao fechamento de um Atendimento.
    }

    /**
     * Salva uma nova evolução clínica ou anotação no prontuário do paciente.
     */
    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'paciente_id'    => 'required|exists:pacientes,id',
            'dentista_id'    => 'required|exists:dentistas,id',
            'atendimento_id' => 'nullable|exists:atendimentos,id', // Opcional, caso seja uma nota isolada
            'descricao'      => 'required|string|min:5',          // Detalhamento do procedimento/queixa
            'prescricao'     => 'nullable|string',                 // Medicamentos receitados, se houver
        ]);

        // Insere a data e hora do registro de forma automatizada pelo servidor por segurança
        $prontuario = Prontuario::create(array_merge($dadosValidados, [
            'data_registro' => now()
        ]));

        return response()->json([
            'message' => 'Evolução clínica registrada no prontuário com sucesso!',
            'data' => $prontuario->load('dentista')
        ], 201);
    }

    /**
     * Exibe os detalhes de um registro específico de prontuário.
     */
    public function show(Prontuario $prontuario)
    {
        $prontuario->load(['paciente', 'dentista', 'atendimento']);
        return response()->json($prontuario);
    }

    /**
     * Retorna os dados para edição do prontuário.
     */
    public function edit(Prontuario $prontuario)
    {
        return response()->json($prontuario);
    }

    /**
     * Permite retificar ou adicionar observações a um prontuário existente.
     */
    public function update(Request $request, Prontuario $prontuario)
    {
        // Regra de Negócio/Legal: Por exigência do CFO/CRO, o conteúdo original de uma evolução 
        // clínica antiga não deve ser livremente apagado ou alterado sem deixar rastro. 
        // Para solucionar isso, o sistema anexa uma nota de retificação (Adendo) ao final do texto.
        $request->validate([
            'justificativa_retificacao' => 'required|string|min:10',
            'texto_adicional'           => 'required|string|min:5'
        ]);

        $textoAtualizado = $prontuario->descricao . "\n\n" . 
            "[RETIFICAÇÃO EM " . now()->format('d/m/Y H:i') . "]: " . $request->texto_adicional . 
            " (Justificativa: " . $request->justificativa_retificacao . ")";

        $prontuario->update([
            'descricao' => $textoAtualizado
        ]);

        return response()->json([
            'message' => 'Termo aditivo anexado ao prontuário com sucesso!',
            'data' => $prontuario
        ]);
    }

    /**
     * Impede ou gerencia a remoção de históricos do prontuário.
     */
    public function destroy(Prontuario $prontuario)
    {
        // Regra de Negócio Absoluta: Prontuários médicos/odontológicos NÃO podem ser deletados 
        // sob nenhuma circunstância ordinária por motivos de resguardo legal da clínica e do profissional.
        return response()->json([
            'error' => 'Por motivos legais de auditoria e conformidade com o CRO, os registros de prontuário são permanentes e não podem ser excluídos do sistema.'
        ], 423);
    }
}