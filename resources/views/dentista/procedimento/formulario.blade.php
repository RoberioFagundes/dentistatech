@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h2 class="fs-3 fw-bold mb-4">{{ isset($procedimento) ? 'Editar Procedimento' : 'Criar Novo Procedimento' }}</h2>

                    <form action="{{ isset($procedimento) ? route('procedimento.update', $procedimento->id) : route('procedimento.store') }}" method="POST">
                        @csrf
                        @isset($procedimento)
                            @method('PUT')
                        @endisset

                        <div class="mb-3">
                            <label for="nome" class="form-label fw-semibold">Nome do Procedimento <span class="text-danger">*</span></label>
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

                        <div class="mb-4">
                            <label for="descricao" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" 
                                      name="descricao" 
                                      rows="4">{{ old('descricao', $procedimento->descricao ?? '') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="valor" class="form-label fw-semibold">Valor (R$) <span class="text-danger">*</span></label>
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

                            <div class="col-md-6">
                                <label for="duracao_minutos" class="form-label fw-semibold">Duração (em minutos) <span class="text-danger">*</span></label>
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
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Status</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ativo" id="ativo_sim" value="1" 
                                        {{ old('ativo', $procedimento->ativo ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo_sim">Ativo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ativo" id="ativo_nao" value="0" 
                                        {{ old('ativo', $procedimento->ativo ?? '1') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo_nao">Inativo</label>
                                </div>
                            </div>
                            @error('ativo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end mt-4">
                            <a href="{{ route('procedimento.index') }}" class="btn btn-secondary order-2 order-sm-1">Cancelar</a>
                            <button type="submit" class="btn btn-primary order-1 order-sm-2">Salvar Procedimento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection