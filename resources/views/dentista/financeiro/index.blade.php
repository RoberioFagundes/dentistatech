@extends('dentista.layout.paginaDentista')
@section('pagina')

<div class="container">
    <h2>Financeiro</h2>
    <a href="{{ route('financeiro.create') }}" class="btn btn-success mb-3">Novo Registro</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Data de Criação</th>
                <th>Forma de Pagamento</th>
                <th>Observação</th>
                <th>Data de Pagamento</th>
                <th>status</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($financeiros as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>R$ {{ number_format($item->valor_pago, 2, ',', '.') }}</td>
                <td><span class="badge bg-info">{{ $item->status_pagamento }}</span></td>
                <td>
                    <a href="{{ route('financeiro.edit', $item->id) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('financeiro.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection