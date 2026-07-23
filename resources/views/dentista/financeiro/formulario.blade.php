@extends('dentista.layout.paginaDentista')
@section('pagina')
<form action="{{ isset($financeiro) ? route('financeiro.update', $financeiro->id) : route('financeiro.store') }}" method="POST">
    @csrf
    @if(isset($financeiro)) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Valor</label>
            <input type="number" step="0.01" name="valor_pago" class="form-control" 
                   value="{{ old('valor_pago', $financeiro->valor_pago ?? '') }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Forma de Pagamento</label>
            <input type="text" name="forma_pagamento" class="form-control" 
                   value="{{ old('forma_pagamento', $financeiro->forma_pagamento ?? '') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status_pagamento" class="form-select">
                @foreach(['PENDENTE', 'PAGO', 'CANCELADO', 'ESTORNADO'] as $status)
                    <option value="{{ $status }}" {{ old('status_pagamento', $financeiro->status_pagamento ?? 'PENDENTE') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Observação</label>
            <textarea name="observacao" class="form-control">{{ old('observacao', $financeiro->observacao ?? '') }}</textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">{{ isset($financeiro) ? 'Atualizar' : 'Salvar' }}</button>
        </div>
    </div>
</form>

@endsection