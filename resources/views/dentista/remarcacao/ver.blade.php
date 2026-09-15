@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-4">
    <h2 class="mb-4">Detalhes da Remarcação</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <p><strong>Paciente:</strong> {{ $remarcacao->agendamento->paciente->nome }} {{ $remarcacao->agendamento->paciente->sobrenome }}</p>
            <p><strong>Data Original:</strong> {{ \Carbon\Carbon::parse($remarcacao->data_anterior)->format('d/m/Y H:i') }}</p>
            <p><strong>Nova Data:</strong> {{ \Carbon\Carbon::parse($remarcacao->data_nova)->format('d/m/Y H:i') }}</p>
            <p><strong>Motivo:</strong> {{ $remarcacao->motivo ?? 'Não informado' }}</p>
            <p><strong>Feito por:</strong> {{ $remarcacao->user->name ?? '—' }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('remarcacao.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection