@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container">
    <h2>{{ isset($procedimento) ? 'Editar Procedimento' : 'Criar Novo Procedimento' }}</h2>

    <form action="{{ isset($procedimento) ? route('procedimento.update', $procedimento->id) : route('procedimento.store') }}" method="POST">
        @csrf
        @isset($procedimento)
            @method('PUT')
        @endisset

        <div class="form-group mb-3">
            <label for="nome" class="form-label">Nome do Procedimento *</label>
            <input type="text" 
                   class="form-control @error('nome') is-invalid @enderror" 
                   id="nome" 
                   name="nome" 
                   maxlength="255" 
                   value="{{ old('nome', $procedimento->nome ?? '') }}" 
                   required>
            @error('nome')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                      id="descricao" 
                      name="descricao" 
                      rows="4">{{ old('descricao', $procedimento->descricao ?? '') }}</textarea>
            @error('descricao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="valor" class="form-label">Valor (R$) *</label>
            <input type="number" 
                   class="form-control @error('valor') is-invalid @enderror" 
                   id="valor" 
                   name="valor" 
                   step="0.01" 
                   min="0" 
                   value="{{ old('valor', $procedimento->valor ?? '0.00') }}" 
                   required>
            @error('valor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="duracao_minutos" class="form-label">Duração (em minutos) *</label>
            <input type="number" 
                   class="form-control @error('duracao_minutos') is-invalid @enderror" 
                   id="duracao_minutos" 
                   name="duracao_minutos" 
                   min="1" 
                   value="{{ old('duracao_minutos', $procedimento->duracao_minutos ?? '30') }}" 
                   required>
            @error('duracao_minutos')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label class="form-label d-block">Status</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="ativo" id="ativo_sim" value="1" 
                    {{ old('ativo', $procedimento->ativo ?? '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="ativo_sim">Ativo</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="ativo" id="ativo_nao" value="0" 
                    {{ old('ativo', $procedimento->ativo ?? '1') == '0' ? 'checked' : '' }}>
                <label class="form-check-label" for="ativo_nao">Inativo</label>
            </div>
            @error('ativo')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Salvar Procedimento</button>
        <a href="{{ route('procedimento.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection