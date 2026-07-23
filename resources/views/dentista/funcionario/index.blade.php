@extends('dentista.layout.paginaDentista')

@section('pagina')
<a href="{{ route('funcionarios.create') }}">
    Cadastrar Novo Funcionário
</a>
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Funcionários da Sua Clínica
</h2>


<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between mb-4">
                <h3 class="text-lg font-medium">Total: {{ $funcionarios->count() }} funcionários</h3>
                <a href="{{ route('funcionarios.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Novo Funcionário</a>
            </div>

            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Nome</th>
                        <th class="p-3">Cargo</th>
                        <th class="p-3">Contato</th>
                        <th class="p-3">Situação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($funcionarios as $f)
                    <tr class="border-t">
                        <td class="p-3">{{ $f->name }}</td>
                        <td class="p-3">{{ ucfirst($f->cargo) }}</td>
                        <td class="p-3">{{ $f->telefone ?? 'Não informado' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded text-xs {{ $f->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $f->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection