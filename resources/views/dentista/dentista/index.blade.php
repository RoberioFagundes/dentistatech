@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container mt-4 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fs-3 fw-bold text-dark mb-1">Dentistas Cadastrados</h1>
            <p class="text-muted small mb-0">Gerencie os profissionais de odontologia registrados na clínica.</p>
        </div>
        <a href="{{ route('dentista.create') }}" class="btn btn-success d-flex align-items-center gap-2 fw-medium px-3 shadow-sm">
            <i class="bi bi-person-plus-fill fs-5"></i> Novo Dentista
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <form method="GET" action="{{ route('dentista.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <input type="text"
                            name="busca"
                            class="form-control"
                            placeholder="Buscar por nome, especialidade ou CRO..."
                            value="{{ request('busca') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Pesquisar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('dentista.index') }}" class="btn btn-outline-secondary w-100">Limpar</a>
                    </div>
                </div>
            </form>
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th scope="col" class="py-3 ps-4 text-secondary small fw-bold text-uppercase" style="width: 25%;">Profissional</th>
                        <th scope="col" class="py-3 text-secondary small fw-bold text-uppercase" style="width: 15%;">CRO</th>
                        <th scope="col" class="py-3 text-secondary small fw-bold text-uppercase" style="width: 20%;">Especialidade</th>
                        <th scope="col" class="py-3 text-secondary small fw-bold text-uppercase" style="width: 25%;">Contatos</th>
                        <th scope="col" class="py-3 pe-4 text-secondary small fw-bold text-uppercase text-center" style="width: 15%;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dentistas as $dentista)
                    <tr class="border-bottom">
                        <td class="py-3 ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-placeholder rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; min-width: 40px;">
                                    {{ mb_substr($dentista->nome, 0, 1) }}{{ mb_substr($dentista->sobrenome, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark">{{ $dentista->nome }} {{ $dentista->sobrenome }}</h6>
                                    <small class="text-muted">{{ $dentista->cidade }} - {{ $dentista->estado }}</small>
                                </div>
                            </div>
                        </td>

                        <td class="py-3">
                            <span class="badge bg-light text-dark border px-2 py-1.5 fw-medium fs-7">
                                {{ $dentista->cro }} / {{ $dentista->cro_uf }}
                            </span>
                        </td>

                        <td class="py-3">
                            <span class="text-dark fw-medium">{{ $dentista->especialidade }}</span>
                        </td>

                        <td class="py-3">
                            <div class="d-flex flex-column gap-1 small text-muted">
                                <div class="d-flex align-items-center gap-1.5" title="WhatsApp">
                                    <i class="bi bi-whatsapp text-success"></i>
                                    <span>{{ $dentista->whatsapp }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5" title="E-mail">
                                    <i class="bi bi-envelope text-secondary"></i>
                                    <span class="text-truncate" style="max-width: 180px;">{{ $dentista->email }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-3 pe-4 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('dentista.edit', $dentista->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 px-2.5 py-1.5" title="Editar Profissional">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>

                                <form action="{{ route('dentista.destroy', $dentista->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o cadastro de {{ $dentista->nome }}? Esta ação não pode ser desfeita.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1 px-2.5 py-1.5" title="Remover Cadastro">
                                        <i class="bi bi-trash3"></i> Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted mb-2">
                                <i class="bi bi-person-badge text-secondary opacity-50" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="text-secondary fw-semibold mb-1 fs-6">Nenhum dentista encontrado</h5>
                            <p class="text-muted small mb-0">Clique no botão "Novo Dentista" para começar os registros.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        {{ $dentistas->links() }}
    </div>

</div>
@endsection