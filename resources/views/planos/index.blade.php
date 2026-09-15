@extends('dentista.layout.paginaDentista')
@section('pagina')

{{-- EXIBE MENSAGENS DE ERRO E SUCESSO --}}
@if($errors->any())
<div class="alert alert-danger m-4 mb-0 rounded-3 shadow-sm">
    <h5 class="alert-heading fw-bold mb-1">Ops! Algo deu errado</h5>
    @foreach ($errors->all() as $erro)
    <p class="mb-0 small">{{ $erro }}</p>
    @endforeach
</div>
@endif

@if(session('sucesso'))
<div class="alert alert-success m-4 mb-0 rounded-3 shadow-sm">
    <h5 class="alert-heading fw-bold mb-1">Sucesso!</h5>
    <p class="mb-0 small">{{ session('sucesso') }}</p>
</div>
@endif

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            
            {{-- SE HOUVER UM PIX GERADO NA SESSÃO, EXIBE O QR CODE --}}
            @if(session('pix_copia_cola'))
                <div class="card shadow border-0 rounded-lg overflow-hidden text-center">
                    <div class="bg-primary text-white py-4 px-3">
                        <span class="fs-1" aria-hidden="true">📱</span>
                        <h2 class="h3 fw-bold mb-1">Pague com Pix para Ativar</h2>
                        <p class="text-white-50 mb-0">Escaneie o QR Code abaixo com o aplicativo do seu banco</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        @if(session('pix_qrcode'))
                            <div class="mb-4">
                                {{-- Exibe a imagem do QR Code em Base64 vinda do Asaas --}}
                                <img src="data:image/png;base64,{{ session('pix_qrcode') }}" alt="QR Code Pix" class="img-fluid border rounded p-2 shadow-sm" style="max-width: 250px;">
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Pix Copia e Cola:</label>
                            <div class="input-group">
                                <input type="text" id="inputCopiaCola" class="form-control text-muted bg-light" value="{{ session('pix_copia_cola') }}" readonly>
                                <button class="btn btn-outline-primary px-4 fw-bold" type="button" onclick="copiarPix()">
                                    📋 Copiar
                                </button>
                            </div>
                            <small id="avisoCopiado" class="text-success fw-semibold mt-1 d-none">Código copiado com sucesso!</small>
                        </div>

                        <div class="alert alert-warning small mb-4">
                            Após realizar o pagamento, sua assinatura será ativada automaticamente em instantes.
                        </div>

                        <a href="{{ route('assinatura.index') }}" class="btn btn-secondary px-4 fw-bold">
                            Já fiz o pagamento / Voltar
                        </a>
                    </div>
                </div>

            {{-- SE A ASSINATURA JÁ ESTIVER PAGA/ATIVA --}}
            @elseif($clinica->status_assinatura === 'ativa')
                <div class="card shadow border-0 rounded-lg overflow-hidden">
                    <div class="bg-success text-white text-center py-5 px-3">
                        <span class="display-3 mb-2" aria-hidden="true">🎉</span>
                        <h2 class="h3 fw-bold mb-2">Sua Assinatura está Ativa!</h2>
                        <p class="text-white-50 mb-0">Sua clínica possui acesso completo a todos os recursos do DentistaTech.</p>
                    </div>

                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="row g-3 justify-content-center mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light">
                                    <span class="text-muted small d-block">Status do Plano</span>
                                    <strong class="text-success fs-5">Plano Profissional Ativo</strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light">
                                    <span class="text-muted small d-block">Próxima Cobrança / Validade</span>
                                    <strong class="text-dark fs-5">
                                        {{ \Carbon\Carbon::now()->addMonth()->format('d/m/Y') }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted small mb-4">
                            Gerenciado de forma segura via Asaas. Caso queira gerenciar ou cancelar sua assinatura, entre em contato com o suporte.
                        </p>

                        <a href="{{ route('dashboard') }}" class="btn btn-primary px-5 py-2 fw-bold">
                            Voltar para o Dashboard
                        </a>
                    </div>
                </div>

            {{-- SE NÃO ESTIVER ATIVA (EXIBE TELA DE PAGAMENTO / TRIAL) --}}
            @else
                <div class="card shadow border-0 rounded-lg overflow-hidden">

                    {{-- Alerta de Período de Teste --}}
                    @if($clinica->onTrial())
                    <div class="alert alert-primary d-flex align-items-center m-4 mb-0 rounded-3 shadow-sm" role="alert">
                        <span class="fs-3 me-3" aria-hidden="true">⏳</span>
                        <div>
                            <h5 class="alert-heading fw-bold mb-1">Período de Teste Ativo</h5>
                            <p class="mb-0 small">Seu teste acaba em <strong>{{ $clinica->trial_ends_at->diffForHumans() }}</strong>. Assine agora para garantir acesso contínuo.</p>
                        </div>
                    </div>
                    @endif

                    <!-- Cabeçalho Azul -->
                    <div class="bg-primary text-white text-center py-5 px-3">
                        <h2 class="h3 fw-bold mb-2">Plano Profissional Completo</h2>
                        <p class="text-white-50 mb-4">Tudo o que sua clínica precisa por um preço justo e transparente</p>
                        <div class="bg-white text-primary d-inline-flex align-items-center px-5 py-2 rounded-pill shadow">
                            <span class="display-4 fw-bold">R$ 150</span>
                            <span class="ms-2 text-muted fw-semibold">/ mês</span>
                        </div>
                    </div>

                    <!-- Lista de Benefícios -->
                    <div class="card-body p-4 p-md-5">
                        <ul class="list-unstyled mb-4">
                            <li class="py-2 text-primary d-flex align-items-center"><span class="me-2" aria-hidden="true">✅</span>Cadastro ilimitado de pacientes</li>
                            <li class="py-2 text-primary d-flex align-items-center"><span class="me-2" aria-hidden="true">✅</span>Cadastro ilimitado de dentistas</li>
                            <li class="py-2 text-primary d-flex align-items-center"><span class="me-2" aria-hidden="true">✅</span>Agenda completa e lembretes inteligentes</li>
                            <li class="py-2 text-primary d-flex align-items-center"><span class="me-2" aria-hidden="true">✅</span>Odontograma interativo avançado</li>
                            <li class="py-2 text-primary d-flex align-items-center"><span class="me-2" aria-hidden="true">✅</span>Controle financeiro e relatórios detalhados</li>
                            <li class="py-2 text-primary d-flex align-items-center"><span class="me-2" aria-hidden="true">✅</span>Emissão profissional de orçamentos</li>
                            <li class="py-2 text-primary d-flex align-items-center"><span class="me-2" aria-hidden="true">✅</span>Suporte técnico prioritário</li>
                        </ul>

                        <!-- Opções de Pagamento -->
                        <form action="{{ route('assinatura.criar') }}" method="POST" id="formAssinatura" novalidate>
                            @csrf
                            <p class="fw-bold text-dark text-uppercase small mb-3">Escolha a Forma de Pagamento</p>

                            <div class="row g-3 mb-4">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="forma_pagamento" id="cc" value="CREDIT_CARD" checked required>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="cc">
                                        <span class="fs-3 mb-1" aria-hidden="true">💳</span>
                                        <span class="small fw-semibold">Crédito</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="forma_pagamento" id="pix" value="PIX" required>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="pix">
                                        <span class="fs-3 mb-1" aria-hidden="true">📱</span>
                                        <span class="small fw-semibold">Pix</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="forma_pagamento" id="boleto" value="BOLETO" required>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="boleto">
                                        <span class="fs-3 mb-1" aria-hidden="true">🧾</span>
                                        <span class="small fw-semibold">Boleto</span>
                                    </label>
                                </div>
                            </div>

                            <!-- CONTAINER DINÂMICO -->
                            <div class="mb-4">
                                <!-- Formulário Cartão -->
                                <div id="container-cc" class="payment-fields p-3 border rounded bg-light">
                                    <h6 class="fw-bold text-dark mb-3">Preencha os dados do Cartão de Crédito</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold" for="holder_name">Nome no Cartão</label>
                                            <input type="text" name="holder_name" id="holder_name" class="form-control" placeholder="Como impresso no cartão" maxlength="100" autocomplete="cc-name">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold" for="number">Número do Cartão</label>
                                            <input type="text" name="number" id="number" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" autocomplete="cc-number">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold" for="expiry">Validade (Mês/Ano)</label>
                                            <input type="text" name="expiry" id="expiry" class="form-control" placeholder="MM/AA" maxlength="5" autocomplete="cc-exp">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold" for="ccv">CVV</label>
                                            <input type="text" name="ccv" id="ccv" class="form-control" placeholder="123" maxlength="4" autocomplete="cc-csc" inputmode="numeric">
                                        </div>
                                    </div>
                                </div>

                                <!-- Aviso PIX -->
                                <div id="container-pix" class="payment-fields p-4 border rounded bg-light d-none text-center">
                                    <span class="fs-1" aria-hidden="true">📱</span>
                                    <h6 class="fw-bold text-dark mt-2">Geração de Pix Instantâneo</h6>
                                    <p class="text-muted small mb-0">Ao confirmar, o QR Code e o código copia-e-cola serão exibidos na próxima tela.</p>
                                </div>

                                <!-- Aviso Boleto -->
                                <div id="container-boleto" class="payment-fields p-4 border rounded bg-light d-none text-center">
                                    <span class="fs-1" aria-hidden="true">🧾</span>
                                    <h6 class="fw-bold text-dark mt-2">Geração de Boleto Bancário</h6>
                                    <p class="text-muted small mb-0">Ao confirmar, o boleto em PDF estará disponível imediatamente.</p>
                                </div>
                            </div>

                            <button type="submit" id="btnSubmit" class="btn btn-primary btn-lg w-100 fw-bold py-3">
                                Finalizar com Cartão de Crédito
                            </button>

                            <p class="text-center text-muted small mt-4 mb-0">
                                Cobrança segura processada via Asaas.<br>
                                Sem contratos de fidelidade — cancele quando quiser.
                            </p>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- SCRIPT PARA COPIAR O PIX E GERENCIAR O FORMULÁRIO --}}
<script>
    function copiarPix() {
        const input = document.getElementById('inputCopiaCola');
        const aviso = document.getElementById('avisoCopiado');
        
        input.select();
        input.setSelectionRange(0, 99999); // Para dispositivos móveis
        
        navigator.clipboard.writeText(input.value).then(() => {
            aviso.classList.remove('d-none');
            setTimeout(() => {
                aviso.classList.add('d-none');
            }, 4000);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('input[name="forma_pagamento"]');
        const containerCc = document.getElementById('container-cc');
        const containerPix = document.getElementById('container-pix');
        const containerBoleto = document.getElementById('container-boleto');
        const btnSubmit = document.getElementById('btnSubmit');
        const formAssinatura = document.getElementById('formAssinatura');

        if (!formAssinatura) return;

        const camposCartao = {
            holder: document.getElementById('holder_name'),
            numero: document.getElementById('number'),
            validade: document.getElementById('expiry'),
            ccv: document.getElementById('ccv')
        };

        function atualizarInterface() {
            const selecionado = document.querySelector('input[name="forma_pagamento"]:checked')?.value;
            if (!selecionado) return;

            containerCc.classList.add('d-none');
            containerPix.classList.add('d-none');
            containerBoleto.classList.add('d-none');

            Object.values(camposCartao).forEach(campo => {
                if(campo) {
                    campo.removeAttribute('required');
                    campo.blur();
                }
            });

            switch (selecionado) {
                case 'CREDIT_CARD':
                    containerCc.classList.remove('d-none');
                    btnSubmit.innerText = 'Finalizar com Cartão de Crédito';
                    Object.values(camposCartao).forEach(campo => { if(campo) campo.setAttribute('required', 'required'); });
                    break;
                case 'PIX':
                    containerPix.classList.remove('d-none');
                    btnSubmit.innerText = 'Gerar Pix e Assinar';
                    break;
                case 'BOLETO':
                    containerBoleto.classList.remove('d-none');
                    btnSubmit.innerText = 'Gerar Boleto e Assinar';
                    break;
            }
        }

        if (camposCartao.numero) {
            camposCartao.numero.addEventListener('input', function() {
                let valor = this.value.replace(/\D/g, '').substring(0, 16);
                this.value = valor.replace(/(\d{4})(?=\d)/g, '$1 ');
            });
        }

        if (camposCartao.validade) {
            camposCartao.validade.addEventListener('input', function() {
                let valor = this.value.replace(/\D/g, '').substring(0, 4);
                this.value = valor.length >= 3 ? `${valor.substring(0,2)}/${valor.substring(2)}` : valor;
            });
        }

        if (camposCartao.ccv) {
            camposCartao.ccv.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });
        }

        formAssinatura.addEventListener('submit', function(e) {
            const selecionado = document.querySelector('input[name="forma_pagamento"]:checked')?.value;

            if (selecionado === 'CREDIT_CARD') {
                const numeroValido = /^\d{4} \d{4} \d{4} \d{4}$/.test(camposCartao.numero.value);
                const validadeValida = /^(0[1-9]|1[0-2])\/\d{2}$/.test(camposCartao.validade.value);
                const ccvValido = /^\d{3,4}$/.test(camposCartao.ccv.value);

                if (!numeroValido || !validadeValida || !ccvValido) {
                    e.preventDefault();
                    alert('Verifique os dados do cartão e tente novamente.');
                    return false;
                }
            }

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processando...';
        });

        atualizarInterface();
        radios.forEach(radio => radio.addEventListener('change', atualizarInterface));
    });
</script>

@endsection