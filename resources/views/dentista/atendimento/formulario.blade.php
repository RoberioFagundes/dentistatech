@extends('dentista.layout.paginaDentista')
@section('pagina')
<div class="container-fluid my-4">
    
    <div class="mb-4">
        <a href="{{ route('atendimento.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 text-muted mb-2">
            <i class="bi bi-arrow-left me-1"></i> Voltar para o histórico
        </a>
        <h4 class="fw-bold text-dark">
            {{ isset($atendimento) ? 'Editar Atendimento #' . $atendimento->id : 'Novo Registro de Atendimento' }}
        </h4>
    </div>

    <form action="{{ isset($atendimento) ? route('atendimento.update', $atendimento->id) : route('atendimento.store') }}" method="POST">
        @csrf
        @if(isset($atendimento))
            @method('PUT')
        @endif

        <input type="hidden" name="procedimento_dente_id" id="procedimento_dente_id" value="{{ old('procedimento_dente_id', $atendimento->procedimento_dente_id ?? '') }}">
        <input type="hidden" name="user_id" value="{{ isset($atendimento) ? $atendimento->user_id : auth()->id() }}">

        <div class="row g-4">
            <div class="col-lg-8">
                
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-people text-primary me-2"></i>Vínculos do Atendimento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="paciente_id" class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
                                <select class="form-select @error('paciente_id') is-invalid @enderror" id="paciente_id" name="paciente_id" required>
                                    <option value="" selected disabled>Selecione...</option>
                                    @foreach($pacientes as $paciente)
                                        <option value="{{ $paciente->id }}" {{ old('paciente_id', $atendimento->paciente_id ?? '') == $paciente->id ? 'selected' : '' }}>
                                            {{ $paciente->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('paciente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="dentista_id" class="form-label fw-semibold">Dentista Responsável <span class="text-danger">*</span></label>
                                <select class="form-select @error('dentista_id') is-invalid @enderror" id="dentista_id" name="dentista_id" required>
                                    <option value="" selected disabled>Selecione...</option>
                                    @foreach($dentistas as $dentista)
                                        <option value="{{ $dentista->id }}" {{ old('dentista_id', $atendimento->dentista_id ?? '') == $dentista->id ? 'selected' : '' }}>
                                            {{ $dentista->nome }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dentista_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="agendamento_id" class="form-label fw-semibold">Agendamento Vinculado</label>
                                <select class="form-select @error('agendamento_id') is-invalid @enderror" id="agendamento_id" name="agendamento_id">
                                    <option value="" selected>Nenhum (Atendimento Direto)</option>
                                    @foreach($agendamentos as $agendamento)
                                        <option value="{{ $agendamento->id }}" {{ old('agendamento_id', $atendimento->agendamento_id ?? '') == $agendamento->id ? 'selected' : '' }}>
                                            Horário: {{ $agendamento->data->format('d/m H:i') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agendamento_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-file-earmark-medical text-primary me-2"></i>Informações Clínicas</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="queixa_principal" class="form-label fw-semibold">Queixa Principal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('queixa_principal') is-invalid @enderror" id="queixa_principal" name="queixa_principal" required value="{{ old('queixa_principal', $atendimento->queixa_principal ?? '') }}" placeholder="Ex: Dor aguda no molar inferior direito">
                            @error('queixa_principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="diagnostico" class="form-label fw-semibold">Diagnóstico</label>
                                <textarea class="form-control" id="diagnostico" name="diagnostico" rows="4" placeholder="Ex: Cárie profunda com comprometimento pulpar...">{{ old('diagnostico', $atendimento->diagnostico ?? '') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tratamento_executado" class="form-label fw-semibold">Tratamento Executado</label>
                                <textarea class="form-control" id="tratamento_executado" name="tratamento_executado" rows="4" placeholder="Ex: Abertura coronária, pulpotomia realizada...">{{ old('tratamento_executado', $atendimento->tratamento_executado ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-activity text-primary me-2"></i>Mapeamento do Dente</h5>
                        <span id="status-odontograma" class="badge {{ isset($atendimento->procedimento_dente_id) ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ isset($atendimento->procedimento_dente_id) ? 'Procedimento do Dente Vinculado' : 'Nenhum dente selecionado' }}
                        </span>
                    </div>
                    <div class="card-body overflow-auto text-center py-4">
                        <h6>Quadrantes Superiores</h6>
                        <div class="d-flex justify-content-center gap-1 mb-4 pb-2 border-bottom">
                            @foreach([18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28] as $dente)
                                <button type="button" class="btn btn-outline-secondary tooth-btn p-2 {{ (old('procedimento_dente_id') || isset($atendimento->procedimento_dente_id)) ? 'tooth-active' : '' }}" data-tooth="{{ $dente }}">
                                    <small class="d-block fw-bold text-muted mb-1">{{ $dente }}</small>
                                    <span class="fs-4">🦷</span>
                                </button>
                                @if($dente == 11) <div class="mx-2 border-start"></div> @endif
                            @endforeach
                        </div>

                        <h6>Quadrantes Inferiores</h6>
                        <div class="d-flex justify-content-center gap-1">
                            @foreach([48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38] as $dente)
                                <button type="button" class="btn btn-outline-secondary tooth-btn p-2" data-tooth="{{ $dente }}">
                                    <span class="fs-4">🦷</span>
                                    <small class="d-block fw-bold text-muted mt-1">{{ $dente }}</small>
                                </button>
                                @if($dente == 41) <div class="mx-2 border-start"></div> @endif
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-3 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-journal-text text-primary me-2"></i>Notas Adicionais</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="observacoes_clinicas" class="form-label fw-semibold">Observações Clínicas Gerais</label>
                            <textarea class="form-control" id="observacoes_clinicas" name="observacoes_clinicas" rows="5" placeholder="Recomendações de repouso, receitas emitidas ou alertas sobre alergias...">{{ old('observacoes_clinicas', $atendimento->observacoes_clinicas ?? '') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">
                                <i class="bi bi-check-circle-fill me-2"></i>Salvar Atendimento
                            </button>
                            <a href="{{ route('atendimento.index') }}" class="btn btn-outline-secondary py-2">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="odontogramaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dente <span id="modal-tooth-number" class="text-primary fw-bold">00</span>: Procedimento Localizado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-procedimento-dente">
                <div class="modal-body">
                    <input type="hidden" id="input-dente-id" name="numero_dente">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Selecione o Procedimento Aplicado</label>
                        <select class="form-select" name="procedimento_id" required>
                            <option value="" selected disabled>Escolha...</option>
                            <option value="1">Restauração</option>
                            <option value="2">Tratamento de Canal</option>
                            <option value="3">Extração</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observações da Face do Dente</label>
                        <textarea class="form-control" name="observacao" rows="2" placeholder="Ex: Face Oclusal / Mesial"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" id="btn-salvar-dente">Vincular ao Atendimento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tooth-btn { min-width: 48px; transition: all 0.15s ease; }
    .tooth-btn:hover { transform: scale(1.08); border-color: #0d6efd!important; }
    .tooth-active { background-color: #e6f0ff!important; border-color: #0d6efd!important; color: #0d6efd; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('odontogramaModal');
    if (!modalElement) return;

    const odontogramaModal = new bootstrap.Modal(modalElement);
    const modalToothNumber = document.getElementById('modal-tooth-number');
    const inputDenteId = document.getElementById('input-dente-id');
    const formDente = document.getElementById('form-procedimento-dente');
    const inputProcedimentoDenteId = document.getElementById('procedimento_dente_id');
    const statusOdontograma = document.getElementById('status-odontograma');

    document.querySelectorAll('.tooth-btn').forEach(button => {
        button.addEventListener('click', function () {
            const numDente = this.getAttribute('data-tooth');
            modalToothNumber.textContent = numDente;
            inputDenteId.value = numDente;
            formDente.reset();
            odontogramaModal.show();
        });
    });

    formDente.addEventListener('submit', function (e) {
        e.preventDefault();
        
        // Simulação do envio AJAX para salvar o procedimento do dente isolado primeiro
        // Substitua o bloco abaixo pelo seu fetch() real apontando para a sua rota
        const simulatedCreatedId = Math.floor(Math.random() * 1000) + 1; // Simula ID retornado do banco
        
        // Passa o ID criado para o formulário principal de atendimentos
        inputProcedimentoDenteId.value = simulatedCreatedId;
        
        // Atualiza a interface
        statusOdontograma.className = "badge bg-success";
        statusOdontograma.textContent = `Dente ${inputDenteId.value} Vinculado com Sucesso!`;
        
        document.querySelectorAll('.tooth-btn').forEach(btn => btn.classList.remove('tooth-active'));
        const denteSelecionado = document.querySelector(`.tooth-btn[data-tooth="${inputDenteId.value}"]`);
        if (denteSelecionado) denteSelecionado.classList.add('tooth-active');

        odontogramaModal.hide();
    });
});
</script>
@endsection