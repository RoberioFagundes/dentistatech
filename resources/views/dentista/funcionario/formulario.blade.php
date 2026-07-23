@extends('dentista.layout.paginaDentista')

@section('pagina')

<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Cadastrar Novo Funcionário
            </h3>
        </div>

        <form method="POST" action="{{ route('funcionarios.store') }}">
            @csrf

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Nome Completo *</label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           name="name"
                           value="{{ old('name') }}"
                           required>

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail *</label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           name="email"
                           value="{{ old('email') }}"
                           required>

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">CPF</label>
                        <input type="text"
                               class="form-control"
                               name="cpf"
                               value="{{ old('cpf') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text"
                               class="form-control"
                               name="telefone"
                               value="{{ old('telefone') }}">
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label">Cargo *</label>

                    <select class="form-select" name="cargo" required>
                        <option value="">Selecione...</option>
                        <option value="recepcionista">Recepcionista</option>
                        <option value="dentista">Dentista</option>
                        <option value="auxiliar">Auxiliar</option>
                        <option value="gestor">Gestor</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data de Admissão</label>

                    <input type="date"
                           class="form-control"
                           name="data_admissao">
                </div>

                <div class="form-check mb-3">

                    <input class="form-check-input"
                           type="checkbox"
                           name="ativo"
                           value="1"
                           checked>

                    <label class="form-check-label">
                        Funcionário ativo
                    </label>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Senha *</label>

                        <input type="password"
                               class="form-control"
                               name="password"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmar Senha *</label>

                        <input type="password"
                               class="form-control"
                               name="password_confirmation"
                               required>
                    </div>

                </div>

            </div>

            <div class="card-footer text-end">

                <a href="{{ route('funcionarios.index') }}"
                   class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Salvar Funcionário
                </button>

            </div>

        </form>

    </div>

</div>

@endsection