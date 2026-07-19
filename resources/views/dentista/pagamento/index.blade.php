@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Pagamentos de Atendimentos</h2>
        <a href="{{ route('pagamento.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Novo Pagamento
        </a>
    </div>

     {{-- ✅ Formulário de Busca --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pagamento.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" 
                           name="busca" 
                           class="form-control" 
                           placeholder="Buscar por nome do paciente ou CPF..."
                           value="{{ request('busca') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    @if(request('busca'))
                        <a href="{{ route('pagamento.index') }}" class="btn btn-secondary w-100">Limpar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Paciente / Orçamento</th>
                            <th width="130">Valor Pago</th>
                            <th width="150">Forma</th>
                            <th width="110">Status</th>
                            <th width="160">Data</th>
                            <th width="120">Saldo Devedor</th>
                            <th width="150" class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pagamentos as $pagamento)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $pagamento->orcamentoPaciente->paciente->nome }} {{ $pagamento->orcamentoPaciente->paciente->sobrenome }}</div>
                                <small class="text-muted">CPF: {{ $pagamento->orcamentoPaciente->paciente->cpf }}</small>
                            </td>
                            <td class="fw-bold text-success">
                                R$ {{ number_format($pagamento->valor_pago, 2, ',', '.') }}
                            </td>
                            <td class="text-capitalize">{{ $pagamento->forma_pagamento }}</td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'PAGO' => 'bg-success text-white',
                                        'PENDENTE' => 'bg-warning text-dark',
                                        'CANCELADO' => 'bg-danger text-white',
                                        'ESTORNADO' => 'bg-secondary text-white'
                                    ];
                                    $classe = $statusClasses[$pagamento->status_pagamento] ?? 'bg-light text-dark';
                                @endphp
                                <span class="badge rounded-pill {{ $classe }} px-3 py-1">
                                    {{ $pagamento->status_pagamento }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $pagamento->created_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="fw-semibold text-danger">
                                R$ {{ number_format($pagamento->orcamentoPaciente->saldo_restante ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('pagamento.edit', $pagamento->id) }}" 
                                   class="btn btn-sm btn-outline-warning me-1" title="Editar">
                                    Editar
                                </a>
                                <form action="{{ route('pagamento.destroy', $pagamento->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Excluir"
                                            onclick="return confirm('Deseja realmente excluir esse pagamento?\nO saldo do orçamento será atualizado automaticamente.')">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                        @if($pagamentos->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Nenhum pagamento cadastrado ainda.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginação --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $pagamentos->links() }}
    </div>
</div>
@endsection