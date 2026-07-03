@extends('dentista.layout.paginaDentista')
@section('pagina')
<div class="container-fluid" style="max-width: 1200px; margin-top: -190px;">
    <div class="mb-4">
        <a href="{{ route('atendimento.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 text-muted mb-2">
            <i class="bi bi-arrow-left me-1"></i> Voltar para o histórico
        </a>
        <h4 class="fw-bold text-dark">
            {{ isset($atendimento) ? 'Editar Atendimento #' . $atendimento->id : 'Novo Registro de Atendimento' }}
        </h4>
    </div>

    {{-- FORMULÁRIO PRINCIPAL ENVOLVENDO TUDO --}}
    <form action="{{ isset($atendimento) ? route('atendimento.update', $atendimento->id) : route('atendimento.store') }}" method="POST">
        @csrf
        @if(isset($atendimento)) @method('PUT') @endif
        
        <input type="hidden" name="user_id" value="{{ isset($atendimento) ? $atendimento->user_id : auth()->id() }}">

        <div class="row g-4">
            {{-- Coluna Esquerda: Dados do Atendimento --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-people text-primary me-2"></i>Vínculos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
                                <select class="form-select" name="paciente_id" required>
                                    <option value="" selected disabled>Selecione...</option>
                                    @foreach($pacientes as $p)
                                        <option value="{{ $p->id }}" {{ old('paciente_id', $atendimento->paciente_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Dentista <span class="text-danger">*</span></label>
                                <select class="form-select" name="dentista_id" required>
                                    @foreach($dentistas as $d)
                                        <option value="{{ $d->id }}" {{ old('dentista_id', $atendimento->dentista_id ?? '') == $d->id ? 'selected' : '' }}>{{ $d->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Agendamento</label>
                                <select class="form-select" name="agendamento_id">
                                    <option value="">Nenhum</option>
                                    @foreach($agendamentos as $a)
                                        <option value="{{ $a->id }}">{{ $a->data->format('d/m H:i') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Seção Clínicas --}}
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Queixa Principal</label>
                            <input type="text" class="form-control" name="queixa_principal" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6"><label>Diagnóstico</label><textarea class="form-control" name="diagnostico" rows="3"></textarea></div>
                            <div class="col-md-6"><label>Tratamento</label><textarea class="form-control" name="tratamento_executado" rows="3"></textarea></div>
                        </div>
                    </div>
                </div>

                {{-- ODONTOGRAMA --}}
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body text-center">
                        <h6>Quadrantes Superiores</h6>
                        <div class="d-flex justify-content-center gap-1 mb-3 flex-wrap">
                            @foreach([18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28] as $dente)
                                <button type="button" class="btn btn-outline-secondary tooth-btn p-2" data-tooth="{{ $dente }}"><small>{{ $dente }}</small><br>🦷</button>
                            @endforeach
                        </div>
                        <h6>Quadrantes Inferiores</h6>
                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                            @foreach([48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38] as $dente)
                                <button type="button" class="btn btn-outline-secondary tooth-btn p-2" data-tooth="{{ $dente }}">🦷<br><small>{{ $dente }}</small></button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- LISTA DE PROCEDIMENTOS (DENTRO DO FORMULÁRIO) --}}
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body" id="lista-procedimentos-selecionados">
                        <h6>Procedimentos vinculados aparecerão aqui...</h6>
                    </div>
                </div>
            </div>

            {{-- Coluna Direita: Notas --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <label class="form-label">Observações Clínicas</label>
                        <textarea class="form-control mb-3" name="observacoes_clinicas" rows="5"></textarea>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Salvar Atendimento</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- MODAL (FORA DO FORMULÁRIO PRINCIPAL) --}}
<div class="modal fade" id="odontogramaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form-procedimento-dente">
                <div class="modal-header"><h5 class="modal-title">Dente <span id="modal-tooth-number"></span></h5></div>
                <div class="modal-body">
                    <input type="hidden" id="input-dente-id" name="numero_dente">
                    <select class="form-select mb-3" name="procedimento_dentes_id" required>
                        <option value="" selected disabled>Escolha o procedimento...</option>
                        @foreach($procedimentoDentes as $p)
                            <option value="{{ $p->id }}">{{ $p->nome }}</option>
                        @endforeach
                    </select>
                    <textarea class="form-control" name="observacao" placeholder="Observações..." rows="2"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Vincular</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tooth-btn { min-width: 45px; }
    .tooth-active { background-color: #d1e7dd !important; border-color: #0f5132 !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('odontogramaModal');
        const odontogramaModal = new bootstrap.Modal(modalElement);
        const formDente = document.getElementById('form-procedimento-dente');
        const listaContainer = document.getElementById('lista-procedimentos-selecionados');
        let procedimentosSelecionados = [];

        document.querySelectorAll('.tooth-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('modal-tooth-number').textContent = this.getAttribute('data-tooth');
                document.getElementById('input-dente-id').value = this.getAttribute('data-tooth');
                odontogramaModal.show();
            });
        });

        formDente.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(formDente);
            procedimentosSelecionados.push({
                id: Date.now(),
                dente: formData.get('numero_dente'),
                procedimento_dentes_id: formData.get('procedimento_dentes_id'),
                observacao: formData.get('observacao'),
                nome_procedimento: formDente.querySelector('select[name="procedimento_dentes_id"] option:checked').text
            });
            
            document.querySelector(`.tooth-btn[data-tooth="${formData.get('numero_dente')}"]`).classList.add('tooth-active');
            renderizarLista();
            odontogramaModal.hide();
            formDente.reset();
        });

        function renderizarLista() {
            if (procedimentosSelecionados.length === 0) return listaContainer.innerHTML = '<h6>Nenhum procedimento vinculado.</h6>';
            listaContainer.innerHTML = '<h6>Procedimentos vinculados:</h6>';
            procedimentosSelecionados.forEach((item, index) => {
                listaContainer.innerHTML += `
                    <div class="alert alert-info py-2 d-flex justify-content-between align-items-center">
                        <span><strong>Dente ${item.dente}:</strong> ${item.nome_procedimento}</span>
                        <input type="hidden" name="procedimentos[${index}][dente]" value="${item.dente}">
                        <input type="hidden" name="procedimentos[${index}][id_procedimento]" value="${item.procedimento_dentes_id}">
                        <input type="hidden" name="procedimentos[${index}][observacao]" value="${item.observacao}">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="window.removerItem(${index})">&times;</button>
                    </div>
                `;
            });
        }

        window.removerItem = function(index) {
            const item = procedimentosSelecionados[index];
            procedimentosSelecionados.splice(index, 1);
            if(!procedimentosSelecionados.some(p => p.dente == item.dente)) {
                document.querySelector(`.tooth-btn[data-tooth="${item.dente}"]`).classList.remove('tooth-active');
            }
            renderizarLista();
        };
    });
</script>
@endsection