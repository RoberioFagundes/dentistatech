@extends('dentista.layout.paginaDentista')

@section('pagina')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 fw-bold text-dark mb-1">{{ isset($dentista) ? 'Editar Dentista' : 'Cadastrar Dentista' }}
                </h1>
                <p class="text-muted small mb-0">
                    {{ isset($dentista) ? 'Atualize as informações do profissional cadastrado no sistema.' : 'Insira as informações do profissional para o registro no sistema.' }}
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center gap-2 fw-medium">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Por favor, corrija os erros identificados no formulário abaixo.</span>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                @isset($dentista)
                    <form action="{{ route('dentista.update', ['dentistum' => $dentista->id]) }}" method="POST">
                        @method('PUT')
                @else
                        <form action="{{ route('dentista.store') }}" method="POST">
                    @endisset
                        @csrf

                        <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                            1. Dados Profissionais</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold text-secondary small">Nome <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome"
                                    name="nome" value="{{ old('nome', $dentista->nome ?? '') }}" required
                                    placeholder="Ex: João">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sobrenome" class="form-label fw-semibold text-secondary small">Sobrenome <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('sobrenome') is-invalid @enderror"
                                    id="sobrenome" name="sobrenome"
                                    value="{{ old('sobrenome', $dentista->sobrenome ?? '') }}" required
                                    placeholder="Ex: Silva dos Santos">
                                @error('sobrenome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="cro" class="form-label fw-semibold text-secondary small">Número do CRO <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control mask-cro @error('cro') is-invalid @enderror" id="cro"
                                    name="cro" value="{{ old('cro', $dentista->cro ?? '') }}" required
                                    placeholder="Ex: 12345">
                                @error('cro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="cro_uf" class="form-label fw-semibold text-secondary small">UF do CRO <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('cro_uf') is-invalid @enderror" id="cro_uf" name="cro_uf"
                                    required>
                                    <option value="" disabled {{ !isset($dentista) && !old('cro_uf') ? 'selected' : '' }}>
                                        Selecione...</option>
                                    @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                                        <option value="{{ $uf }}" {{ old('cro_uf', $dentista->cro_uf ?? '') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                                @error('cro_uf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label for="especialidade" class="form-label fw-semibold text-secondary small">Especialidade
                                    Principal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('especialidade') is-invalid @enderror"
                                    id="especialidade" name="especialidade"
                                    value="{{ old('especialidade', $dentista->especialidade ?? '') }}" required
                                    placeholder="Ex: Ortodontia">
                                @error('especialidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="text-muted my-4 opacity-25">

                        <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                            2. Meios de Contato</h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="telefone" class="form-label fw-semibold text-secondary small">Telefone <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control mask-telefone @error('telefone') is-invalid @enderror" id="telefone"
                                    name="telefone" value="{{ old('telefone', $dentista->telefone ?? '') }}" required
                                    placeholder="(00) 0000-0000">
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="whatsapp" class="form-label fw-semibold text-secondary small">WhatsApp <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control mask-telefone @error('whatsapp') is-invalid @enderror" id="whatsapp"
                                    name="whatsapp" value="{{ old('whatsapp', $dentista->whatsapp ?? '') }}" required
                                    placeholder="(00) 00000-0000">
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="email" class="form-label fw-semibold text-secondary small">E-mail Comercial
                                    <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email', $dentista->email ?? '') }}" required
                                    placeholder="dentista@clinica.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="text-muted my-4 opacity-25">

                        <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                            3. Localização Residencial / Comercial</h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="cep" class="form-label fw-semibold text-secondary small">CEP <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control mask-cep @error('cep') is-invalid @enderror" id="cep"
                                    name="cep" value="{{ old('cep', $dentista->cep ?? '') }}" required
                                    placeholder="00000-000">
                                @error('cep')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="cep-error" class="text-danger small mt-1 d-none">CEP não encontrado.</div>
                            </div>

                            <div class="col-md-3">
                                <label for="pais" class="form-label fw-semibold text-secondary small">País <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('pais') is-invalid @enderror" id="pais"
                                    name="pais" value="{{ old('pais', $dentista->pais ?? 'Brasil') }}" required
                                    placeholder="Ex: Brasil">
                                @error('pais')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="estado" class="form-label fw-semibold text-secondary small">Estado <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('estado') is-invalid @enderror" id="estado"
                                    name="estado" value="{{ old('estado', $dentista->estado ?? '') }}" required
                                    placeholder="Ex: São Paulo">
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="cidade" class="form-label fw-semibold text-secondary small">Cidade <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade"
                                    name="cidade" value="{{ old('cidade', $dentista->cidade ?? '') }}" required
                                    placeholder="Ex: Capital">
                                @error('cidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="bairro" class="form-label fw-semibold text-secondary small">Bairro <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro"
                                    name="bairro" value="{{ old('bairro', $dentista->bairro ?? '') }}" required
                                    placeholder="Ex: Centro">
                                @error('bairro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label for="rua" class="form-label fw-semibold text-secondary small">Rua <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('rua') is-invalid @enderror" id="rua"
                                    name="rua" value="{{ old('rua', $dentista->rua ?? '') }}" required
                                    placeholder="Ex: Rua Exemplo">
                                @error('rua')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="numero" class="form-label fw-semibold text-secondary small">Número <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('numero') is-invalid @enderror" id="numero"
                                    name="numero" value="{{ old('numero', $dentista->numero ?? '') }}" required
                                    placeholder="Ex: 123">
                                @error('numero')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="text-muted my-4 opacity-25">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dentista.index') }}" class="btn btn-light border px-4 fw-medium">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 fw-medium d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle"></i> Salvar Registro
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/imask"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 1. Máscaras de Inputs
            const telefones = document.querySelectorAll('.mask-telefone');
            telefones.forEach(function (input) {
                IMask(input, {
                    mask: [
                        { mask: '(00) 0000-0000' },
                        { mask: '(00) 00000-0000' }
                    ]
                });
            });

            const croInput = document.querySelector('.mask-cro');
            if (croInput) {
                IMask(croInput, { mask: '000000' });
            }

            const cepInput = document.querySelector('.mask-cep');
            let cepMask;
            if (cepInput) {
                cepMask = IMask(cepInput, { mask: '00000-000' });
            }

            // 2. Integração com a API de CEP (ViaCEP)
            if (cepInput) {
                cepInput.addEventListener('blur', function () {
                    const cep = cepMask ? cepMask.unmaskedValue : cepInput.value.replace(/\D/g, '');
                    const errorDiv = document.getElementById('cep-error');

                    if (cep.length === 8) {
                        const ruaField = document.getElementById('rua');
                        const bairroField = document.getElementById('bairro');
                        const cityField = document.getElementById('cidade');
                        const estadoField = document.getElementById('estado');

                        errorDiv.classList.add('d-none');
                        [ruaField, bairroField, cityField, estadoField].forEach(el => el.placeholder = 'Buscando...');

                        fetch(`https://viacep.com.br/ws/${cep}/json/`)
                            .then(response => response.json())
                            .then(data => {
                                if (!data.erro) {
                                    ruaField.value = data.logradouro;
                                    bairroField.value = data.bairro;
                                    cityField.value = data.localidade;
                                    estadoField.value = data.uf;

                                    document.getElementById('numero').focus();
                                } else {
                                    errorDiv.classList.remove('d-none');
                                    limpaCamposEndereco();
                                }
                            })
                            .catch(error => {
                                console.error('Erro ao buscar o CEP:', error);
                                limpaCamposEndereco();
                            })
                            .finally(() => {
                                ruaField.placeholder = 'Ex: Rua Exemplo';
                                bairroField.placeholder = 'Ex: Centro';
                                cityField.placeholder = 'Ex: Capital';
                                estadoField.placeholder = 'Ex: São Paulo';
                            });
                    }
                });
            }

            function limpaCamposEndereco() {
                document.getElementById('rua').value = '';
                document.getElementById('bairro').value = '';
                document.getElementById('cidade').value = '';
                document.getElementById('estado').value = '';
            }
        });
    </script>
@endpush