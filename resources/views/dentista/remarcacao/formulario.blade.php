@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-4">
    <h2 class="mb-4">Nova Remarcação de Agendamento</h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('remarcacao.store') }}">
                @csrf

                {{-- Se vier pré-carregado do agendamento --}}
                @if($agendamento)
                <div class="mb-3 bg-light p-3 rounded">
                    <h5 class="mb-2">Agendamento Selecionado</h5>
                    <p class="mb-1"><strong>Paciente:</strong> {{ $agendamento->paciente->nome }} {{ $agendamento->paciente->sobrenome }}</p>
                    <p class="mb-1"><strong>Data Original:</strong> {{ $agendamento->data?->format('d/m/Y') }}</p>
                    <p class="mb-0"><strong>Horário:</strong> {{ $agendamento->hora_inicio }} às {{ $agendamento->hora_fim }}</p>
                    
                    <input type="hidden" name="agendamento_id" value="{{ $agendamento->id }}">
                    <input type="hidden" name="data_anterior" value="{{ $agendamento->data?->format('Y-m-d') }}">
                </div>
                @else
                {{-- Seleção manual do agendamento --}}
                <div class="mb-3">
                    <label for="agendamento_id" class="form-label">Selecionar Agendamento *</label>
                    <select name="agendamento_id" id="agendamento_id" class="form-select" required>
                        <option value="">Escolha um agendamento...</option>
                        @foreach($agendamentos as $ag)
                        <option value="{{ $ag->id }}"
                            data-data="{{ $ag->data?->format('Y-m-d') }}"
                            data-horario="{{ $ag->hora_inicio }} - {{ $ag->hora_fim }}"
                            {{ old('agendamento_id') == $ag->id ? 'selected' : '' }}>
                            {{ $ag->paciente->nome }} — {{ $ag->data?->format('d/m/Y') }} {{ $ag->hora_inicio }}h
                        </option>
                        @endforeach
                    </select>
                    @error('agendamento_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Data/Hora Anterior</label>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="date" name="data_anterior" id="data_anterior"
                                class="form-control @error('data_anterior') is-invalid @enderror"
                                value="{{ old('data_anterior') }}" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control bg-light" id="horario_anterior" readonly placeholder="Horário">
                        </div>
                    </div>
                    @error('data_anterior') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @endif

                {{-- Nova Data e Horário --}}
                <div class="mb-3">
                    <label class="form-label">Nova Data e Horário *</label>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <input type="date" name="data_nova" id="data_nova"
                                class="form-control @error('data_nova') is-invalid @enderror"
                                value="{{ old('data_nova') }}" required>
                        </div>
                        <div class="col-md-3">
                            <input type="time" name="hora_inicio_nova"
                                class="form-control @error('hora_inicio_nova') is-invalid @enderror"
                                value="{{ old('hora_inicio_nova') }}" required placeholder="Início">
                        </div>
                        <div class="col-md-3">
                            <input type="time" name="hora_fim_nova"
                                class="form-control @error('hora_fim_nova') is-invalid @enderror"
                                value="{{ old('hora_fim_nova') }}" required placeholder="Fim">
                        </div>
                    </div>
                    @error('hora_inicio_nova') <div class="text-danger small d-block mb-1">{{ $message }}</div> @enderror
                    @error('hora_fim_nova') <div class="text-danger small d-block">{{ $message }}</div> @enderror
                </div>

                {{-- Motivo --}}
                <div class="mb-3">
                    <label for="motivo" class="form-label">Motivo da Remarcação</label>
                    <textarea name="motivo" id="motivo" rows="3" maxlength="255"
                        class="form-control @error('motivo') is-invalid @enderror">{{ old('motivo') }}</textarea>
                    <small class="text-muted">Máximo de 255 caracteres</small>
                    @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('remarcacao.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Confirmar Remarcação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preenche dados anteriores automaticamente
document.getElementById('agendamento_id')?.addEventListener('change', function() {
    const opcao = this.options[this.selectedIndex];
    document.getElementById('data_anterior').value = opcao.dataset.data || '';
    document.getElementById('horario_anterior').value = opcao.dataset.horario || '';
});
</script>
@endsection