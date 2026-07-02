@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container-fluid my-4 px-4">
    
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
    .font-weight-bold { font-weight: 700 !important; }
    .font-weight-medium { font-weight: 500 !important; }
    .border-b { border-bottom: 1px solid #dee2e6 !important; }
    /* Cores suaves de fundo para os badges no Bootstrap 5 */
    .bg-success-subtle { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .bg-primary-subtle { background-color: #cfe2ff !important; color: #084298 !important; }
    .bg-warning-subtle { background-color: #fff3cd !important; color: #664d03 !important; }
</style>
@endsection