@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light py-3">
        <h5 class="card-title mb-0 text-dark fw-bold"><i class="bi bi-activity me-2 text-primary"></i>Odontograma Interativo</h5>
    </div>
    <div class="card-body">
        
        <div class="row text-center mb-4">
            <div class="col-6 d-flex justify-content-end gap-1 border-end">
                @foreach([18, 17, 16, 15, 14, 13, 12, 11] as $dente)
                    <button type="button" class="btn btn-outline-secondary tooth-btn p-2 d-flex flex-column align-items-center position-relative" data-tooth="{{ $dente }}">
                        <span class="fs-7 fw-bold text-muted mb-1">{{ $dente }}</span>
                        <span class="fs-3">🦷</span>
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-circle p-1 d-none code-badge"></span>
                    </button>
                @endforeach
            </div>
            
            <div class="col-6 d-flex justify-content-start gap-1">
                @foreach([21, 22, 23, 24, 25, 26, 27, 28] as $dente)
                    <button type="button" class="btn btn-outline-secondary tooth-btn p-2 d-flex flex-column align-items-center position-relative" data-tooth="{{ $dente }}">
                        <span class="fs-7 fw-bold text-muted mb-1">{{ $dente }}</span>
                        <span class="fs-3">🦷</span>
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-circle p-1 d-none code-badge"></span>
                    </button>
                @endforeach
            </div>
        </div>

        <hr class="my-4 border-2 opacity-25">

        <div class="row text-center">
            <div class="col-6 d-flex justify-content-end gap-1 border-end">
                @foreach([48, 47, 46, 45, 44, 43, 42, 41] as $dente)
                    <button type="button" class="btn btn-outline-secondary tooth-btn p-2 d-flex flex-column align-items-center position-relative" data-tooth="{{ $dente }}">
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-circle p-1 d-none code-badge"></span>
                        <span class="fs-3">🦷</span>
                        <span class="fs-7 fw-bold text-muted mt-1">{{ $dente }}</span>
                    </button>
                @endforeach
            </div>
            
            <div class="col-6 d-flex justify-content-start gap-1">
                @foreach([31, 32, 33, 34, 35, 36, 37, 38] as $dente)
                    <button type="button" class="btn btn-outline-secondary tooth-btn p-2 d-flex flex-column align-items-center position-relative" data-tooth="{{ $dente }}">
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-circle p-1 d-none code-badge"></span>
                        <span class="fs-3">🦷</span>
                        <span class="fs-7 fw-bold text-muted mt-1">{{ $dente }}</span>
                    </button>
                @endforeach
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="odontogramaModal" tabindex="-1" aria-labelledby="odontogramaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="odontogramaModalLabel">Dente <span id="modal-tooth-number" class="text-primary fw-bold">00</span>: Lançar Procedimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-procedimento-dente">
                <div class="modal-body">
                    <input type="hidden" id="input-dente-id" name="numero_dente">
                    
                    <input type="hidden" name="atendimento_id" value="{{ $atendimento->id ?? '' }}">

                    <div class="mb-3">
                        <label for="select-procedimento" class="form-label fw-medium">Selecione o Procedimento</label>
                        <select class="form-select" id="select-procedimento" name="procedimento_id" required>
                            <option value="" selected disabled>Escolha um procedimento...</option>
                            <option value="1">Obturação de Resina</option>
                            <option value="2">Tratamento de Canal (Endodontia)</option>
                            <option value="3">Extração (Exodontia)</option>
                            <option value="4">Limpeza / Profilaxia</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="textarea-observacao" class="form-label fw-medium">Observações (Opcional)</label>
                        <textarea class="form-control" id="textarea-observacao" name="observacao" rows="3" placeholder="Detalhes da face do dente (Oclusal, Mesial, etc...)"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-salvar-procedimento">Salvar no Dente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tooth-btn {
        min-width: 52px;
        transition: all 0.2s ease-in-out;
    }
    .tooth-btn:hover {
        transform: translateY(-3px);
        border-color: #0d6efd !important;
        background-color: rgba(13, 110, 253, 0.05) !important;
    }
    .tooth-active {
        background-color: #e6f0ff !important;
        border-color: #0d6efd !important;
    }
    .fs-7 { font-size: 0.8rem; }
</style>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Instancia o modal do Bootstrap 5
    const odontogramaModal = new bootstrap.Modal(document.getElementById('odontogramaModal'));
    const modalToothNumber = document.getElementById('modal-tooth-number');
    const inputDenteId = document.getElementById('input-dente-id');
    const formProcedimento = document.getElementById('form-procedimento-dente');

    // Mapeia o clique em cada dente
    document.querySelectorAll('.tooth-btn').forEach(button => {
        button.addEventListener('click', function () {
            const numeroDente = this.getAttribute('data-tooth');
            
            // Atualiza os dados no modal
            modalToothNumber.textContent = numeroDente;
            inputDenteId.value = numeroDente;
            
            // Limpa o formulário de escolhas anteriores
            formProcedimento.reset();

            // Abre o modal
            odontogramaModal.show();
        });
    });

    // Evento de envio do formulário do dente via AJAX
    formProcedimento.addEventListener('submit', function (e) {
        e.preventDefault();

        const btnSalvar = document.getElementById('btn-salvar-procedimento');
        btnSalvar.disabled = true;
        btnSalvar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

        // Captura todos os dados do formulário
        const formData = new FormData(formProcedimento);
        const numeroDente = inputDenteId.value;

        // Rota de envio (Adapte para sua rota real do Laravel)
        const url = '/atendimentos/salvar-procedimento-dente';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                // Token CSRF obrigatório do Laravel procurado na meta tag da página
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Erro na requisição.');
            return response.json();
        })
        .then(data => {
            // Sucesso: Estiliza o dente para mostrar que possui procedimento ativo
            const denteBotao = document.querySelector(`.tooth-btn[data-tooth="${numeroDente}"]`);
            if (denteBotao) {
                denteBotao.classList.add('tooth-active');
                
                // Exibe a bolinha vermelha de notificação no dente
                const badge = denteBotao.querySelector('.code-badge');
                if (badge) badge.classList.remove('d-none');
            }

            // Fecha o modal
            odontogramaModal.hide();
            
            // Alerta amigável (pode trocar por SweetAlert2 se usar no projeto)
            alert(`Procedimento salvo com sucesso no dente ${numeroDente}!`);
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Não foi possível salvar o procedimento. Tente novamente.');
        })
        .finally(() => {
            // Restaura o botão do modal
            btnSalvar.disabled = false;
            btnSalvar.textContent = 'Salvar no Dente';
        });
    });
});

</script>