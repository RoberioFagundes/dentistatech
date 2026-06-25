<?php

namespace App\Http\Controllers;

use App\Models\Odontograma;
use App\Models\Paciente;
use Illuminate\Http\Request;

class OdontogramaController extends Controller
{
    /**
     * Exibe o mapa/histórico do odontograma de um paciente específico.
     */
    public function index(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id'
        ]);

        // Retorna todas as marcações dos dentes do paciente
        // Incluindo o atendimento correspondente se houver para auditoria
        $odontograma = Odontograma::with('atendimento')
            ->where('paciente_id', $request->paciente_id)
            ->get();

        return response()->json($odontograma);
    }

    /**
     * Não é comumente utilizado em APIs de odontograma, 
     * pois a renderização depende dos IDs dos dentes passados dinamicamente.
     */
    public function create()
    {
        //
    }

    /**
     * Salva uma nova marcação em um dente (ou atualiza se já existir).
     */
    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'paciente_id'    => 'required|exists:pacientes,id',
            'atendimento_id' => 'nullable|exists:atendimentos,id',
            'numero_dente'   => 'required|integer|between:11,85', // Notação FDI (11 a 48 permanentes, 51 a 85 decíduos)
            'face_dente'     => 'required|string|in:Oclusal,Mesial,Distal,Vestibular,Lingual,Palatina,Geral',
            'status_dente'   => 'required|string|max:100', // Ex: 'Cárie', 'Restaurado', 'Hígido', 'Ausente'
            'observacoes'    => 'nullable|string'
        ]);

        // Regra de Negócio/Otimização: Se já existir uma marcação idêntica para o mesmo dente e face daquele paciente,
        // nós atualizamos o registro (updateOrCreate) em vez de duplicar linhas no banco de dados.
        $registro = Odontograma::updateOrCreate(
            [
                'paciente_id'  => $request->paciente_id,
                'numero_dente' => $request->numero_dente,
                'face_dente'   => $request->face_dente,
            ],
            [
                'atendimento_id' => $request->atendimento_id,
                'status_dente'   => $request->status_dente,
                'observacoes'    => $request->observacoes
            ]
        );

        return response()->json([
            'message' => 'Odontograma atualizado com sucesso!',
            'data' => $registro
        ], 200);
    }

    /**
     * Exibe o histórico de um registro específico do odontograma.
     */
    public function show(Odontograma $odontograma)
    {
        return response()->json($odontograma);
    }

    /**
     * Retorna os dados para edição.
     */
    public function edit(Odontograma $odontograma)
    {
        return response()->json($odontograma);
    }

    /**
     * Atualiza uma marcação específica do odontograma.
     */
    public function update(Request $request, Odontograma $odontograma)
    {
        $dadosValidados = $request->validate([
            'status_dente' => 'required|string|max:100',
            'observacoes'  => 'nullable|string'
        ]);

        $odontograma->update($dadosValidados);

        return response()->json([
            'message' => 'Marcação alterada com sucesso!',
            'data' => $odontograma
        ]);
    }

    /**
     * Remove uma marcação (Volta o estado da face do dente para "Hígido/Padrão").
     */
    public function destroy(Odontograma $odontograma)
    {
        $odontograma->delete();

        return response()->json(['message' => 'Marcação removida do odontograma com sucesso.']);
    }

    /**
     * MÉTODO ADICIONAL (Altamente recomendado para Odontogramas):
     * Salva múltiplas alterações de uma vez só (Bulk Save).
     * Evita que o front-end faça 10 requisições HTTP seguidas ao marcar vários dentes.
     */
    public function salvarEmLote(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'atendimento_id' => 'nullable|exists:atendimentos,id',
            'itens' => 'required|array',
            'itens.*.numero_dente' => 'required|integer|between:11,85',
            'itens.*.face_dente' => 'required|string|in:Oclusal,Mesial,Distal,Vestibular,Lingual,Palatina,Geral',
            'itens.*.status_dente' => 'required|string|max:100',
            'itens.*.observacoes' => 'nullable|string'
        ]);

        $salvos = [];

        foreach ($request->itens as $item) {
            $salvos[] = Odontograma::updateOrCreate(
                [
                    'paciente_id'  => $request->paciente_id,
                    'numero_dente' => $item['numero_dente'],
                    'face_dente'   => $item['face_dente'],
                ],
                [
                    'atendimento_id' => $request->atendimento_id,
                    'status_dente'   => $item['status_dente'],
                    'observacoes'    => $item['observacoes'] ?? null
                ]
            );
        }

        return response()->json([
            'message' => 'Todos os dentes foram atualizados em lote com sucesso!',
            'data' => $salvos
        ]);
    }
}