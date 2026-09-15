@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Detalhes do Funcionário</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Nome:</strong>
                        <div>{{ $funcionario->name ?? 'N/D' }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>E-mail:</strong>
                        <div>{{ $funcionario->email ?? 'N/D' }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>Telefone:</strong>
                        <div>{{ $funcionario->telefone ?? 'N/D' }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>Cargo:</strong>
                        <div>{{ $funcionario->cargo ?? 'N/D' }}</div>
                    </div>
                    <!-- <div class="mb-3">
                        <strong>Endereço:</strong>
                        <div>{{ $funcionario->endereco ?? 'N/D' }}</div>
                    </div> -->
                    <div class="mb-3">
                        <strong>Data de criação:</strong>
                        <div>{{ optional($funcionario->created_at)->format('d/m/Y H:i') ?? 'N/D' }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>Última atualização:</strong>
                        <div>{{ optional($funcionario->updated_at)->format('d/m/Y H:i') ?? 'N/D' }}</div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
