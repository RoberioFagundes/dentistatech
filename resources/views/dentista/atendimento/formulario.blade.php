@extends('dentista.layout.paginaDentista')
@section('pagina')
@php
$atendimento = $atendimento ?? null;

// Evita duplicidade ao carregar procedimentos salvos
$procedimentosSalvos = collect();
if ($atendimento?->exists) {
$procedimentosSalvos = $atendimento->procedimentos
->unique(fn($p) => $p->id . '-' . ($p->pivot->numero_dente ?? ''));
}

$procedimentosOpcoes = $procedimentoDentes->keyBy('id');
$usuarioLogadoId = auth()->id();
$dentistaLogadoId = auth()->user()?->dentista?->id ?? null;
@endphp

<div class="container-fluid py-3" style="max-width: 1400px;">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('atendimento.index') }}"
                class="btn btn-sm btn-link text-decoration-none p-0 text-muted mb-2">
                <i class="bi bi-arrow-left me-1"></i> Voltar para o histórico
            </a>
            <h4 class="fw-bold text-dark mb-1">
                {{ $atendimento ? 'Editar Atendimento #' . $atendimento->id : 'Novo Registro de Atendimento' }}
            </h4>
            <p class="text-muted mb-0">Registre ou edite os dados clínicos e procedimentos do atendimento.</p>
        </div>
    </div>

    <form
        action="{{ $atendimento ? route('atendimento.update', $atendimento->id) : route('atendimento.store') }}"
        method="POST">
        @csrf
        @if($atendimento) @method('PUT') @endif

        <input type="hidden" name="user_id" value="{{ old('user_id', $atendimento->user_id ?? $usuarioLogadoId) }}">

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Vínculos --}}
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i
                                class="bi bi-people text-primary me-2"></i>Vínculos</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Dentista <span class="text-danger">*</span></label>
                                <select class="form-select" name="dentista_id" required>
                                    @foreach($dentistas as $d)
                                    <option value="{{ $d->id }}"
                                        {{ old('dentista_id', $atendimento?->dentista_id ?? $dentistaLogadoId) == $d->id ? 'selected' : '' }}>
                                        Dr(a). {{ $d->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Paciente e Agendamento</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <select class="form-select" name="paciente_id" {{ isset($pacienteId) ? 'disabled' : 'required' }}>
                                            <option value="" selected disabled>Selecione o paciente...</option>
                                            @foreach($pacientes as $p)
                                            <option value="{{ $p->id }}"
                                                {{ (isset($pacienteId) && $pacienteId == $p->id) || old('paciente_id', $atendimento->paciente_id ?? '') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nome }} {{ $p->sobrenome }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @if(isset($pacienteId))
                                        <input type="hidden" name="paciente_id" value="{{ $pacienteId }}">
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" name="agendamento_id" {{ isset($agendamentoId) ? 'disabled' : '' }}>
                                            <option value="">Nenhum agendamento</option>
                                            @foreach($agendamentos as $a)
                                            <option value="{{ $a->id }}"
                                                {{ (isset($agendamentoId) && $agendamentoId == $a->id) || old('agendamento_id', $atendimento->agendamento_id ?? '') == $a->id ? 'selected' : '' }}>
                                                {{ $a->data->format('d/m H:i') }} - {{ $a->paciente->nome ?? 'Paciente sem nome' }}
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

                {{-- Dados Clínicos --}}
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Queixa Principal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="queixa_principal"
                                placeholder="Descreva a queixa principal do paciente" required
                                value="{{ old('queixa_principal', $atendimento->queixa_principal ?? '') }}">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Diagnóstico</label>
                                <textarea class="form-control" name="diagnostico" rows="3"
                                    placeholder="Descrição do diagnóstico clínico">{{ old('diagnostico', $atendimento->diagnostico ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tratamento Executado</label>
                                <textarea class="form-control" name="tratamento_executado" rows="3"
                                    placeholder="Procedimentos e tratamentos realizados">{{ old('tratamento_executado', $atendimento->tratamento_executado ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Odontograma --}}
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body text-center p-4">
                        <h6 class="mb-3 fw-semibold">Odontograma</h6>
                        <p class="text-muted small mb-3">Clique em um dente para vincular um procedimento</p>
                        <div class="d-flex justify-content-center gap-2 mb-2 flex-wrap">
                            @foreach([18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28] as $d)
                            <button type="button" class="btn btn-outline-secondary tooth-btn p-2" data-tooth="{{ $d }}">
                                <small>{{ $d }}</small><br>🦷
                            </button>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            @foreach([48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38] as $d)
                            <button type="button" class="btn btn-outline-secondary tooth-btn p-2" data-tooth="{{ $d }}">
                                🦷<br><small>{{ $d }}</small>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Procedimentos Selecionados --}}
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4" id="lista-procedimentos-selecionados">
                        <h6 class="text-muted mb-0">Procedimentos vinculados aparecerão aqui...</h6>
                    </div>
                </div>
            </div>

            {{-- Resumo Lateral --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top rounded-4" style="top: 1rem;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Resumo do Atendimento</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Observações Clínicas</label>
                            <textarea class="form-control" name="observacoes_clinicas" rows="5"
                                placeholder="Notas adicionais, cuidados ou recomendações">{{ old('observacoes_clinicas', $atendimento->observacoes_clinicas ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Validade do Orçamento</label>
                            <input type="date" class="form-control" name="validade"
                                value="{{ old('validade', $atendimento?->orcamento?->validade?->format('Y-m-d') ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Valor Total</label>
                            <input type="number" step="0.01" class="form-control" name="total_orcamento"
                                id="total_orcamento" readonly
                                value="{{ old('total_orcamento', $atendimento?->orcamento?->total_orcamento ?? '0.00') }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Desconto (R$)</label>
                            <input type="number" step="0.01" class="form-control" name="desconto" id="desconto"
                                value="{{ old('desconto', $atendimento?->orcamento?->desconto ?? '0.00') }}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Salvar Atendimento</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modal do Odontograma --}}
<div class="modal fade" id="odontogramaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <form id="form-procedimento-dente">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Dente <span id="modal-tooth-number"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="input-dente-id" name="numero_dente">
                    <div class="mb-3">
                        <label class="form-label">Procedimento <span class="text-danger">*</span></label>
                        <select id="select-procedimento" class="form-select" name="procedimento_dentes_id" required>
                            <option value="" disabled selected>Escolha o procedimento...</option>
                            @foreach($procedimentoDentes as $p)
                            <option value="{{ $p->id }}" data-valor="{{ $p->valor }}">
                                {{ $p->nome }} - R$ {{ number_format($p->valor, 2, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Observação</label>
                        <textarea class="form-control" name="observacao" rows="2" placeholder="Detalhes adicionais..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Vincular Procedimento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tooth-btn {
        min-width: 50px;
        border-radius: 1rem;
        transition: all .15s ease;
    }

    .tooth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .tooth-active {
        background: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #fff !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .15);
    }

    #lista-procedimentos-selecionados .list-group-item {
        border: 1px solid #e9ecef;
        border-radius: .5rem;
        margin-bottom: .5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap === 'undefined') return console.error('Bootstrap não encontrado!');
        const modal = new bootstrap.Modal(document.getElementById('odontogramaModal'));
        const formDente = document.getElementById('form-procedimento-dente');
        const lista = document.getElementById('lista-procedimentos-selecionados');
        let procedimentos = [];

        // Carrega procedimentos já salvos no atendimento
        const salvos = @json($procedimentosSalvos->values(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        const opcoes = @json($procedimentosOpcoes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        if (salvos.length > 0) {
            salvos.forEach(item => {
                procedimentos.push({
                    id: Date.now() + Math.random(),
                    dente: item.pivot?.numero_dente,
                    procedimento_id: item.id,
                    valor: parseFloat(item.valor || 0),
                    observacao: item.pivot?.observacao || '',
                    nome: opcoes[item.id]?.nome || 'Procedimento'
                });
            });
            procedimentos.forEach(p => document.querySelector(`.tooth-btn[data-tooth="${p.dente}"]`)?.classList.add('tooth-active'));
        }

        // Abre modal ao clicar no dente
        document.querySelectorAll('.tooth-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('modal-tooth-number').textContent = btn.dataset.tooth;
                document.getElementById('input-dente-id').value = btn.dataset.tooth;
                modal.show();
            });
        });

        // Adiciona novo procedimento
        formDente.addEventListener('submit', e => {
            e.preventDefault();
            const sel = document.getElementById('select-procedimento');
            procedimentos.push({
                id: Date.now(),
                dente: document.getElementById('input-dente-id').value,
                procedimento_id: sel.value,
                valor: parseFloat(sel.selectedOptions[0].dataset.valor || 0),
                observacao: formDente.querySelector('[name=observacao]').value.trim(),
                nome: sel.selectedOptions[0].textContent.trim()
            });
            document.querySelector(`.tooth-btn[data-tooth="${procedimentos.at(-1).dente}"]`)?.classList.add('tooth-active');
            modal.hide();
            formDente.reset();
            renderizar();
        });

        // Remove procedimento da lista
        window.removerItem = idx => {
            const item = procedimentos[idx];
            procedimentos.splice(idx, 1);
            if (!procedimentos.some(p => p.dente == item.dente)) {
                document.querySelector(`.tooth-btn[data-tooth="${item.dente}"]`)?.classList.remove('tooth-active');
            }
            renderizar();
        };

        // Calcula valor total automaticamente
        const calcularTotal = () => {
            let totalBruto = procedimentos.reduce((s, i) => s + i.valor, 0);
            let desconto = parseFloat(document.getElementById('desconto').value) || 0;
            document.getElementById('total_orcamento').value = (totalBruto - desconto).toFixed(2);
        };
        document.getElementById('desconto').addEventListener('input', calcularTotal);

        // Atualiza a lista de procedimentos na tela
        function renderizar() {
            if (!procedimentos.length) {
                lista.innerHTML = '<h6 class="text-muted mb-0">Procedimentos vinculados aparecerão aqui...</h6>';
                return calcularTotal();
            }
            lista.innerHTML = '<h6 class="mb-3 fw-semibold">Procedimentos Vinculados</h6>';
            procedimentos.forEach((item, i) => {
                lista.innerHTML += `
        <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
            <div>
                <strong>Dente ${item.dente}</strong>
                <div class="text-muted small">${item.nome}</div>
                <div class="text-primary small fw-semibold">Valor: R$ ${item.valor.toFixed(2).replace('.', ',')}</div>
                ${item.observacao ? `<div class="text-muted small mt-1">Obs: ${item.observacao}</div>` : ''}
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerItem(${i})">
                <i class="bi bi-trash me-1"></i>Remover
            </button>
            {{-- ✅ CAMPOS CORRIGIDOS PARA O CONTROLLER --}}
            <input type="hidden" name="procedimentos[${i}][dente]" value="${item.dente}">
            <input type="hidden" name="procedimentos[${i}][procedimento_id]" value="${item.procedimento_id}">
            <input type="hidden" name="procedimentos[${i}][observacao]" value="${item.observacao}">
            <input type="hidden" name="procedimentos[${i}][valor]" value="${item.valor}">
            <input type="hidden" name="procedimentos[${i}][quantidade]" value="1">
            <input type="hidden" name="procedimentos[${i}][face]" value="">
        </div>`;
            });
            calcularTotal();
        }
    });
</script>
@endsection