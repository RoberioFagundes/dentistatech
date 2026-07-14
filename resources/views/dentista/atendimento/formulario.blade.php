@extends('dentista.layout.paginaDentista')
@section('pagina')
@php
// Garante que a variável exista mesmo no modo novo
$atendimento = $atendimento ?? null;
$procedimentosSalvos = $atendimento?->procedimentos ?? collect();
$procedimentosOpcoes = $procedimentoDentes->keyBy('id');
@endphp

<div class="container-fluid" style="max-width: 1400px; margin-top: -190px;">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('atendimento.index') }}"
                class="btn btn-sm btn-link text-decoration-none p-0 text-muted mb-2">
                <i class="bi bi-arrow-left me-1"></i> Voltar para o histórico
            </a>
            <h4 class="fw-bold text-dark mb-1">
                {{ isset($atendimento) ? 'Editar Atendimento #' . $atendimento->id : 'Novo Registro de Atendimento' }}
            </h4>
            <p class="text-muted mb-0">Registre ou edite os dados clínicos e procedimentos do atendimento.</p>
        </div>
    </div>

    <form
        action="{{ isset($atendimento) ? route('atendimento.update', $atendimento->id) : route('atendimento.store') }}"
        method="POST">
        @csrf
        @if(isset($atendimento)) @method('PUT') @endif

        <input type="hidden" name="user_id" value="{{ old('user_id', $atendimento->user_id ?? auth()->id()) }}">

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i
                                class="bi bi-people text-primary me-2"></i>Vínculos</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Dentista <span
                                        class="text-danger">*</span></label>
                                <select class="form-select shadow-sm" name="dentista_id" required>
                                    @foreach($dentistas as $d)
                                    <option value="{{ $d->id }}" {{ old('dentista_id', $atendimento->dentista_id ?? '') == $d->id ? 'selected' : '' }}>{{ $d->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Agendamento</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <select class="form-select shadow-sm" name="paciente_id" {{ isset($pacienteId) ? 'disabled' : 'required' }}>
                                            <option value="" selected disabled>Selecione o paciente...</option>
                                            @foreach($pacientes as $p)
                                            <option value="{{ $p->id }}" {{ (isset($pacienteId) && $pacienteId == $p->id) || old('paciente_id', $atendimento->paciente_id ?? '') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nome }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if(isset($pacienteId))
                                        <input type="hidden" name="paciente_id" value="{{ $pacienteId }}">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select shadow-sm" name="agendamento_id" {{ isset($agendamentoId) ? 'disabled' : '' }}>
                                            <option value="">Nenhum</option>
                                            @foreach($agendamentos as $a)
                                            <option value="{{ $a->id }}" {{ (isset($agendamentoId) && $agendamentoId == $a->id) || old('agendamento_id', $atendimento->agendamento_id ?? '') == $a->id ? 'selected' : '' }}>
                                                {{ $a->data->format('d/m H:i') }} -
                                                {{ $a->paciente->nome ?? 'Paciente sem nome' }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if(isset($agendamentoId))
                                        <input type="hidden" name="agendamento_id" value="{{ $agendamentoId }}">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Queixa Principal</label>
                            <input type="text" class="form-control shadow-sm" name="queixa_principal"
                                placeholder="Digite a queixa principal" required
                                value="{{ old('queixa_principal', $atendimento->queixa_principal ?? '') }}">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Diagnóstico</label>
                                <textarea class="form-control shadow-sm" name="diagnostico" rows="3"
                                    placeholder="Descrição do diagnóstico">{{ old('diagnostico', $atendimento->diagnostico ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tratamento</label>
                                <textarea class="form-control shadow-sm" name="tratamento_executado" rows="3"
                                    placeholder="Procedimentos realizados">{{ old('tratamento_executado', $atendimento->tratamento_executado ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body text-center p-4">
                        <h6 class="mb-3 fw-semibold">Odontograma</h6>
                        <div class="d-flex justify-content-center gap-2 mb-3 flex-wrap">
                            @foreach([18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28] as $dente)
                            <button type="button" class="btn btn-outline-secondary tooth-btn p-2"
                                data-tooth="{{ $dente }}"><small>{{ $dente }}</small><br>🦷</button>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            @foreach([48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38] as $dente)
                            <button type="button" class="btn btn-outline-secondary tooth-btn p-2"
                                data-tooth="{{ $dente }}">🦷<br><small>{{ $dente }}</small></button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4" id="lista-procedimentos-selecionados">
                        <h6 class="text-muted mb-0">Procedimentos vinculados aparecerão aqui...</h6>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top rounded-4" style="top: 20px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Resumo do atendimento</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Observações Clínicas</label>
                            <textarea class="form-control shadow-sm" name="observacoes_clinicas" rows="5"
                                placeholder="Notas adicionais">{{ old('observacoes_clinicas', $atendimento->observacoes_clinicas ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Data de Validade do orçamento</label>
                            <input type="date" class="form-control shadow-sm" name="validade" id="validade"
                                value="{{ old('validade', $atendimento?->orcamento?->validade?->format('Y-m-d') ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Valor do orçamento</label>
                            <input type="number" step="0.01" class="form-control shadow-sm" name="total_orcamento"
                                id="total_orcamento" placeholder="0,00" readonly
                                value="{{ old('total_orcamento', $atendimento?->orcamento?->total_orcamento ?? '0.00') }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Desconto</label>
                            <input type="number" step="0.01" class="form-control shadow-sm" name="desconto" id="desconto"
                                placeholder="0,00" value="{{ old('desconto', $atendimento?->orcamento?->desconto ?? '0.00') }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Salvar Atendimento</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="odontogramaModal" tabindex="-1" aria-labelledby="modal-tooth-number" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden">
            <form id="form-procedimento-dente">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Dente <span id="modal-tooth-number"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="input-dente-id" name="numero_dente">
                    <select class="form-select mb-3 shadow-sm" name="procedimento_dentes_id" id="select-procedimento"
                        required>
                        <option value="" selected disabled>Escolha o procedimento...</option>
                        @foreach($procedimentoDentes as $p)
                        <option value="{{ $p->id }}" data-valor="{{ $p->valor }}">{{ $p->nome }} - R$
                            {{ number_format($p->valor, 2, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    <textarea class="form-control shadow-sm" name="observacao" placeholder="Observações..."
                        rows="2"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Vincular</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tooth-btn {
        min-width: 50px;
        border-radius: 1rem;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .tooth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, .08);
    }

    .tooth-active {
        background-color: #0d6efd !important;
        border-color: #0b5ed7 !important;
        color: #fff !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #7c7fff;
        box-shadow: 0 0 0 .2rem rgba(124, 124, 255, .15);
    }

    #lista-procedimentos-selecionados .list-group-item {
        border: 1px solid #eef2ff;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Valida se o Bootstrap está carregado
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap não encontrado! Verifique o layout.');
        return;
    }

    const modalElement = document.getElementById('odontogramaModal');
    if (!modalElement) {
        console.error('Modal não encontrado!');
        return;
    }

    const odontogramaModal = new bootstrap.Modal(modalElement);
    const formDente = document.getElementById('form-procedimento-dente');
    const listaContainer = document.getElementById('lista-procedimentos-selecionados');
    let procedimentosSelecionados = [];

    // Carrega procedimentos salvos
    const procedimentosSalvos = @json($procedimentosSalvos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    const procedimentosOpcoes = @json($procedimentosOpcoes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    if (procedimentosSalvos.length > 0) {
        procedimentosSalvos.forEach(item => {
            procedimentosSelecionados.push({
                id: Date.now() + Math.random(),
                dente: item.pivot?.dente ?? item.dente ?? null,
                procedimento_dentes_id: item.id ?? item.procedimento_id,
                valor: parseFloat(item.pivot?.valor ?? item.valor ?? 0),
                observacao: item.pivot?.observacao ?? item.observacao ?? '',
                nome_procedimento: procedimentosOpcoes[item.id ?? item.procedimento_id]?.nome ?? 'Procedimento não encontrado'
            });
        });
        marcarDentesCarregados();
        renderizarLista();
    }

    function marcarDentesCarregados() {
        procedimentosSelecionados.forEach(item => {
            if (item.dente) {
                const botaoDente = document.querySelector(`.tooth-btn[data-tooth="${item.dente}"]`);
                if (botaoDente) botaoDente.classList.add('tooth-active');
            }
        });
    }

    // Abre o modal ao clicar no dente
    document.querySelectorAll('.tooth-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const numeroDente = this.dataset.tooth;
            document.getElementById('modal-tooth-number').textContent = numeroDente;
            document.getElementById('input-dente-id').value = numeroDente;
            odontogramaModal.show();
        });
    });

    formDente.addEventListener('submit', function(e) {
        e.preventDefault();
        const select = document.getElementById('select-procedimento');
        const valorProcedimento = parseFloat(select.options[select.selectedIndex].getAttribute('data-valor')) || 0;
        const formData = new FormData(formDente);
        const numeroDente = formData.get('numero_dente');

        procedimentosSelecionados.push({
            id: Date.now(),
            dente: numeroDente,
            procedimento_dentes_id: formData.get('procedimento_dentes_id'),
            valor: valorProcedimento,
            observacao: formData.get('observacao'),
            nome_procedimento: select.options[select.selectedIndex].text
        });

        const botaoDente = document.querySelector(`.tooth-btn[data-tooth="${numeroDente}"]`);
        if (botaoDente) botaoDente.classList.add('tooth-active');

        renderizarLista();
        odontogramaModal.hide();
        formDente.reset();
    });

    function calcularTotal() {
        let total = procedimentosSelecionados.reduce((sum, item) => sum + parseFloat(item.valor), 0);
        let desconto = parseFloat(document.getElementById('desconto').value) || 0;
        let totalFinal = total - desconto;
        document.getElementById('total_orcamento').value = totalFinal.toFixed(2);
    }

    document.getElementById('desconto').addEventListener('input', calcularTotal);

    window.removerItem = function(index) {
        const item = procedimentosSelecionados[index];
        procedimentosSelecionados.splice(index, 1);
        if (!procedimentosSelecionados.some(p => p.dente == item.dente)) {
            const botaoDente = document.querySelector(`.tooth-btn[data-tooth="${item.dente}"]`);
            if (botaoDente) botaoDente.classList.remove('tooth-active');
        }
        renderizarLista();
    };

    function renderizarLista() {
        if (procedimentosSelecionados.length === 0) {
            listaContainer.innerHTML = '<h6 class="text-muted mb-0">Procedimentos vinculados aparecerão aqui...</h6>';
        } else {
            listaContainer.innerHTML = '<h6 class="mb-3">Procedimentos vinculados</h6>';
            procedimentosSelecionados.forEach((item, index) => {
                listaContainer.innerHTML += `
                    <div class="list-group-item rounded-3 d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <strong>Dente ${item.dente}</strong>
                            <div class="text-muted small">${item.nome_procedimento} - R$ ${item.valor.toFixed(2).replace('.', ',')}</div>
                            ${item.observacao ? `<div class="text-muted fst-italic small mt-1">Obs: ${item.observacao}</div>` : ''}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="window.removerItem(${index})">Remover</button>
                        <input type="hidden" name="procedimentos[${index}][dente]" value="${item.dente}">
                        <input type="hidden" name="procedimentos[${index}][procedimento_id]" value="${item.procedimento_dentes_id}">
                        <input type="hidden" name="procedimentos[${index}][observacao]" value="${item.observacao}">
                        <input type="hidden" name="procedimentos[${index}][valor]" value="${item.valor}">
                    </div>
                `;
            });
        }
        calcularTotal();
    }
});
</script>

@endsection