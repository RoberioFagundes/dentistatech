@extends('dentista.layout.paginaDentista')

@section('pagina')

<div class="card p-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h2 class="fs-4 fw-bold text-dark mb-1">Funcionários da Sua Clínica</h2>
            <p class="text-muted mb-0">Gerencie todos os profissionais cadastrados</p>
        </div>
        <a href="{{ route('funcionarios.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Cadastrar Novo Funcionário
        </a>
    </div>
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-semibold mb-0">Total: {{ $funcionarios->count() }} funcionários</h5>
        <a href="{{ route('funcionarios.create') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-person-plus me-1"></i>Novo
        </a>
    </div>

    @if($funcionarios->isEmpty())
        <div class="alert alert-info text-center">
            Nenhum funcionário cadastrado ainda.
        </div>
    @else
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Contato</th>
                    <th class="text-center">Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($funcionarios as $f)
                <tr>
                    <td>{{ $f->name }}</td>
                    <td>{{ ucfirst($f->cargo) }}</td>
                    <td>{{ $f->telefone ?? 'Não informado' }}</td>
                    <td class="text-center">
                        @if($f->ativo)
                            <span class="badge bg-success">Ativo</span>
                        @else
                            <span class="badge bg-danger">Inativo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('funcionarios.show', $f->id) }}" class="btn btn-sm btn-outline-secondary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('funcionarios.edit', $f->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('funcionarios.destroy', $f->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection