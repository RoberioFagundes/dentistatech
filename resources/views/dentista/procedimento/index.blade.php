@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-4">

    <!-- Cabeçalho -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h2 class="fs-3 fw-bold mb-0">Procedimentos Cadastrados</h2>
        <a href="{{ route('procedimento.create') }}" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i> Novo Procedimento
        </a>
    </div>

    <!-- Alerta de Sucesso -->
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    <!-- Área de Busca e Filtro -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body pb-0">
            <form method="GET" action="{{ route('procedimento.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="busca" class="form-control"
                               placeholder="Buscar por nome ou código clínico..."
                               value="{{ request('busca') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="ativo" class="form-select">
                            <option value="">Todos os status</option>
                            <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Apenas Ativos</option>
                            <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Apenas Inativos</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        <a href="{{ route('procedimento.index') }}" class="btn btn-outline-secondary w-100">Limpar</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabela Responsiva -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th scope="col" class="ps-4" style="width: 8%;">ID</th>
                        <th scope="col" style="width: 25%;">Procedimento</th>
                        <th scope="col" class="d-none d-lg-table-cell" style="width: 30%;">Descrição</th>
                        <th scope="col" style="width: 15%;">Valor</th>
                        <th scope="col" class="d-none d-md-table-cell" style="width: 12%;">Duração</th>
                        <th scope="col" style="width: 10%;">Status</th>
                        <th scope="col" class="pe-4 text-center" style="width: 15%;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procedimentos as $procedimento)
                    <tr>
                        <td class="ps-4 text-muted">{{ $procedimento->id }}</td>
                        <td>
                            <strong>{{ $procedimento->nome }}</strong>
                            <small class="d-lg-none text-muted d-block" title="{{ $procedimento->descricao ?? 'Sem descrição' }}">
                                {{ Str::limit($procedimento->descricao, 40) ?? 'Sem descrição' }}
                            </small>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="text-muted text-truncate d-inline-block" style="max-width: 280px;"
                                  title="{{ $procedimento->descricao }}">
                                {{ $procedimento->descricao ?? 'Sem descrição' }}
                            </span>
                        </td>
                        <td class="fw-semibold text-primary">R$ {{ number_format($procedimento->valor, 2, ',', '.') }}</td>
                        <td class="d-none d-md-table-cell">{{ $procedimento->duracao_minutos }} min</td>
                        <td>
                            @if($procedimento->ativo)
                            <span class="badge bg-success">Ativo</span>
                            @else
                            <span class="badge bg-danger">Inativo</span>
                            @endif
                        </td>
                        <td class="pe-4 text-center">
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-1">
                                <a href="{{ route('procedimento.edit', $procedimento->id) }}"
                                   class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center gap-1"
                                   title="Editar procedimento">
                                    <i class="bi bi-pencil"></i>
                                    <span class="d-none d-sm-inline">Editar</span>
                                </a>
                                <form action="{{ route('procedimento.destroy', $procedimento->id) }}" method="POST"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este procedimento?');"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center gap-1"
                                            title="Excluir procedimento">
                                        <i class="bi bi-trash"></i>
                                        <span class="d-none d-sm-inline">Excluir</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-clipboard-x text-secondary opacity-50" style="font-size: 3rem;"></i>
                            <h5 class="text-secondary fw-semibold mt-2 mb-1">Nenhum procedimento encontrado</h5>
                            <p class="text-muted small mb-0">Cadastre um novo procedimento para começar.</p>
                        </td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    <div class="mt-4 d-flex justify-content-center justify-content-md-end">
        {{ $procedimentos->links() }}
    </div>

</div>
@endsection