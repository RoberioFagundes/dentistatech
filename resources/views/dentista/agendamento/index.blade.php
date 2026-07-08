@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container-fluid my-4 px-4">
    <form action="{{ route('agendamento.index') }}" method="GET" class="row">
        <div class="col-md-4">
            <input type="text" name="nome" class="form-control" placeholder="Buscar por nome..." value="{{ $nomeFiltro }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="data" class="form-control" value="{{ $dataFiltro }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('agendamento.index') }}" class="btn btn-secondary">Limpar</a>
        </div>
    </form>

    <div class="d-flex flex-col flex-md-row justify-content-between align-items-md-center border-b pb-3 mb-4 gap-3">
        <div>
            <h2 class="h3 mb-1 text-dark font-weight-bold">Controle de Agendamentos</h2>
            <p class="text-muted small mb-0">Gerencie os horários, pacientes e status dos atendimentos no consultório.</p>
        </div>
        <div>
            <a href="{{ route('agendamento.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm px-3 py-2">
                <span>➕</span> Novo Agendamento
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="table-light text-secondary text-uppercase small font-weight-bold">
                    <tr>
                        <th scope="col" class="px-4 py-3">Data / Hora</th>
                        <th scope="col" class="px-3 py-3">Paciente</th>
                        <th scope="col" class="px-3 py-3">Dentista / Profissional</th>
                        <th scope="col" class="px-3 py-3">Procedimento</th>
                        <th scope="col" class="px-3 py-3">Status</th>
                        <th scope="col" class="px-3 py-3">Obs.</th>
                        <th scope="col" class="px-4 py-3 text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agendamentos as $item)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-weight-bold text-dark">
                                {{ \Carbon\Carbon::parse($item->data)->format('d/m/Y') }}
                            </div>
                            <div class="text-muted small">
                                {{ \Carbon\Carbon::parse($item->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->hora_fim)->format('H:i') }}
                            </div>
                        </td>

                        <td class="px-3 py-3 text-dark font-weight-medium">
                            {{ $item->paciente->nome ?? 'Não informado' }}
                        </td>

                        <td class="px-3 py-3 text-secondary">

                            Dr(a). {{ $item->dentista->nome ?? 'Não informado' }}
                        </td>

                        <td class="px-3 py-3 text-muted">
                            {{ $item->procedimento->nome ?? '-' }}
                        </td>

                        <td class="px-3 py-3">
                            @switch($item->status)
                            @case('CONFIRMADO')
                            <span class="badge bg-success-subtle text-success px-2.5 py-1.5 rounded-pill small">
                                Confirmado
                            </span>
                            @break
                            @case('EM_ATENDIMENTO')
                            <span class="badge bg-primary-subtle text-primary px-2.5 py-1.5 rounded-pill small placeholder-glow">
                                Em Atendimento
                            </span>
                            @break
                            @case('CANCELADO')
                            <span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 rounded-pill small">
                                Cancelado
                            </span>
                            @break
                            @default
                            <span class="badge bg-warning-subtle text-warning px-2.5 py-1.5 rounded-pill small">
                                Agendado
                            </span>
                            @endswitch
                        </td>

                        <td class="px-3 py-3 text-muted small text-truncate" style="max-width: 150px;" title="{{ $item->observacao }}">
                            {{ $item->observacao ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <form action="{{ route('fila.adicionar') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="agendamento_id" value="{{ $item->id }}">
                                    <input type="hidden" name="paciente_id" value="{{ $item->paciente_id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary border-0 text-primary p-1.5" title="Adicionar à Fila">
                                        👥
                                    </button>
                                </form>
                                <a href="{{ route('agendamento.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary border-0 text-primary p-1.5" title="Editar">
                                    ✏️
                                </a>

                                <form action="{{ route('agendamento.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?');"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary border-0 text-danger p-1.5" title="Excluir">
                                        ❌
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-5 text-center text-secondary bg-white">
                            <div class="py-4">
                                <span class="display-6 text-muted mb-2 d-block">📭</span>
                                <p class="mb-0 text-muted">Nenhum agendamento localizado para os critérios informados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Auxiliares para compatibilidade de estilos de peso de fonte */
    .font-weight-bold {
        font-weight: 700 !important;
    }

    .font-weight-medium {
        font-weight: 500 !important;
    }

    .border-b {
        border-bottom: 1px solid #dee2e6 !important;
    }

    /* Cores suaves de fundo para os badges no Bootstrap 5 */
    .bg-success-subtle {
        background-color: #d1e7dd !important;
        color: #0f5132 !important;
    }

    .bg-primary-subtle {
        background-color: #cfe2ff !important;
        color: #084298 !important;
    }

    .bg-warning-subtle {
        background-color: #fff3cd !important;
        color: #664d03 !important;
    }
</style>


@endsection

<script>
    document.querySelectorAll('.btn-adicionar-fila').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const agendamentoId = this.getAttribute('data-agendamento-id');
            const pacienteId = this.getAttribute('data-paciente-id');

            // Chama a função que criamos anteriormente
            adicionarNaFila(agendamentoId, pacienteId);
        });
    });

    // A função que processa a requisição (ajustada para seu controller)
    async function adicionarNaFila(agendamentoId, pacienteId) {
        try {
            // Primeira e única declaração do 'response'
            const response = await fetch('/fila-atendimento/adicionar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    agendamento_id: agendamentoId,
                    paciente_id: pacienteId
                })
            });

            // Verifique o status antes de tentar ler o JSON
            if (response.ok) {
                const data = await response.json();
                alert('Sucesso: ' + data.message);
                location.reload();
            } else {
                // Se o servidor retornar erro 500 ou 422, lemos o JSON de erro aqui
                const data = await response.json().catch(() => ({}));
                console.error('Detalhes do erro:', data);
                alert('Erro no servidor: ' + (data.error || 'Falha na conexão.'));
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            alert('Erro de rede: verifique o console.');
        }
    }
</script>