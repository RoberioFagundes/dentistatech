@extends('dentista.layout.paginaDentista')

@section('pagina')
    <div class="container mt-4">

        <!-- Cabeçalho com Botão de Criar Novo -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Procedimentos Cadastrados</h2>
            <a href="{{ route('procedimento.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Novo Procedimento
            </a>
        </div>

        <!-- Mensagens de Sucesso do Sistema -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabela de procedimento -->
        <div class="table-responsive card shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 5%;">ID</th>
                        <th scope="col" style="width: 25%;">Procedimento</th>
                        <th scope="col" style="width: 30%;">Descrição</th>
                        <th scope="col" style="width: 12%;">Valor</th>
                        <th scope="col" style="width: 10%;">Duração</th>
                        <th scope="col" style="width: 8%;">Status</th>
                        <th scope="col" style="width: 10%;" class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procedimentos as $procedimento)
                        <tr>
                            <td>{{ $procedimento->id }}</td>
                            <td><strong>{{ $procedimento->nome }}</strong></td>
                            <td>
                                <span class="text-muted text-truncate d-inline-block" style="max-width: 250px;"
                                    title="{{ $procedimento->descricao }}">
                                    {{ $procedimento->descricao ?? 'Sem descrição' }}
                                </span>
                            </td>
                            <!-- Formatação do Decimal (8,2) para moeda brasileira -->
                            <td>R$ {{ number_format($procedimento->valor, 2, ',', '.') }}</td>
                            <!-- Exibição do Inteiro de minutos -->
                            <td>{{ $procedimento->duracao_minutos }} min</td>
                            <!-- Badge dinâmica baseada no tinyint (0 ou 1) -->
                            <td>
                                @if($procedimento->ativo)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                            <!-- Botões de Ação -->
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Link para o formulário de edição (passando o ID) -->
                                    <a href="{{ route('procedimento.edit', $procedimento->id) }}" class="btn btn-primary"
                                        title="Editar">
                                        Editar
                                    </a>

                                    <!-- Formulário para exclusão segura com método DELETE -->
                                    <form action="{{ route('procedimento.destroy', $procedimento->id) }}" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja excluir este procedimento?');"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Excluir">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Nenhum procedimento encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Links de Paginação (Caso use ->paginate() no Controller) -->
        <div class="mt-3">
            {{ $procedimentos->links() }}
        </div>

    </div>
@endsection