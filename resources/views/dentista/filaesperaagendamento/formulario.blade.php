@extends('dentista.layout.paginaDentista')

@section('pagina')

<form action="{{ isset($fila->id) ? route('filaAgendamentoEspera.update', $fila->id) : route('filaAgendamentoEspera.store') }}" method="POST" enctype="multipart/form-data">
    @if(isset($fila->id)) @method('PUT') @endif
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <label for="pessoa_id" class="form-label">Paciente</label>
            <select name="pessoa_id" id="pessoa_id" class="form-select" required>
                <option value="">Selecione um paciente...</option>
                @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ (isset($fila) && $fila->pessoa_id == $paciente->id) ? 'selected' : '' }}>
                        {{ $paciente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="dias_urgencia" class="form-label">Dias de Urgência</label>
            {{-- Altere para isto --}}
            <input type="number" name="dias_urgencia" id="dias_urgencia" class="form-control" value="{{ old('dias_urgencia', (isset($fila) && !($fila instanceof \Illuminate\Support\Collection)) ? $fila->dias_urgencia : 0) }}">
        </div>

        <div class="mb-4">
            <label for="arquivoInput" class="form-label fw-medium" id="labelArquivoInput">
                Anexar Arquivo <span class="text-muted small" id="infoOpcional">(Documentos/Exames)</span>
            </label>
            <input type="file" name="arquivoInput" id="arquivoInput" class="form-control @error('arquivoInput') is-invalid @enderror">
            @error('arquivoInput')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="motivo" class="form-label">Motivo</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="3" required>{{ old('motivo', (isset($fila) && !($fila instanceof \Illuminate\Support\Collection)) ? $fila->motivo : '') }}</textarea>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-success">{{ isset($fila) ? 'Salvar Alterações' : 'Adicionar à Fila' }}</button>
        </div>
    </div>
</form>






@endsection
