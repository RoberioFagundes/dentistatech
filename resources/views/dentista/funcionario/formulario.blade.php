@extends('dentista.layout.paginaDentista')

@section('pagina')
@php
    $isEdit = isset($funcionario);
@endphp
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title mb-0">{{ $isEdit ? 'Editar Funcionário' : 'Cadastrar Novo Funcionário' }}</h3>
        </div>

        <form method="POST" action="{{ $isEdit ? route('funcionarios.update', $funcionario) : route('funcionarios.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row g-3">
                    <!-- Nome -->
                    <div class="col-12">
                        <label class="form-label">Nome Completo *</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               name="name"
                               value="{{ old('name', $funcionario->name ?? '') }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- E-mail -->
                    <div class="col-12">
                        <label class="form-label">E-mail *</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email', $funcionario->email ?? '') }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- CPF -->
                    <div class="col-md-6">
                        <label class="form-label">CPF</label>
                        <input type="text"
                               class="form-control @error('cpf') is-invalid @enderror"
                               name="cpf"
                               value="{{ old('cpf', $funcionario->cpf ?? '') }}">
                        @error('cpf')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Telefone -->
                    <div class="col-md-6">
                        <label class="form-label">Telefone</label>
                        <input type="text"
                               class="form-control @error('telefone') is-invalid @enderror"
                               name="telefone"
                               value="{{ old('telefone', $funcionario->telefone ?? '') }}">
                        @error('telefone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Cargo -->
                    <div class="col-12">
                        <label class="form-label">Cargo *</label>
                        <select class="form-select @error('cargo') is-invalid @enderror" name="cargo" required>
                            <option value="">Selecione...</option>
                            <option value="recepcionista" {{ old('cargo', $funcionario->cargo ?? '') == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                            <option value="dentista" {{ old('cargo', $funcionario->cargo ?? '') == 'dentista' ? 'selected' : '' }}>Dentista</option>
                            <option value="auxiliar" {{ old('cargo', $funcionario->cargo ?? '') == 'auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                            <option value="gestor" {{ old('cargo', $funcionario->cargo ?? '') == 'gestor' ? 'selected' : '' }}>Gestor</option>
                        </select>
                        @error('cargo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Data de Admissão -->
                    <div class="col-md-6">
                        <label class="form-label">Data de Admissão</label>
                        <input type="date"
                               class="form-control @error('data_admissao') is-invalid @enderror"
                               name="data_admissao"
                               value="{{ old('data_admissao', $funcionario->data_admissao ?? '') }}">
                        @error('data_admissao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Situação -->
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check mb-0">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="ativo"
                                   value="1"
                                   {{ old('ativo', isset($funcionario) ? $funcionario->ativo : true) ? 'checked' : '' }}>
                            <label class="form-check-label">
                                Funcionário ativo
                            </label>
                        </div>
                        @error('ativo')
                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Senha -->
                    <div class="col-md-6">
                        <label class="form-label">Senha {{ $isEdit ? '' : '*' }}</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password"
                               @unless($isEdit) required @endunless>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirmar Senha -->
                    <div class="col-md-6">
                        <label class="form-label">Confirmar Senha {{ $isEdit ? '' : '*' }}</label>
                        <input type="password"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               name="password_confirmation"
                               @unless($isEdit) required @endunless>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex flex-column flex-sm-row justify-content-end gap-2">
                <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary order-2 order-sm-1">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary order-1 order-sm-2">
                    {{ $isEdit ? 'Atualizar Funcionário' : 'Salvar Funcionário' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection