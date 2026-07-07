@extends('dentista.layout.paginaDentista')

@section('pagina')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid py-4">

        @if (session()->has('sucesso-agendamentos'))
            <div class="alert alert-success alert-dismissible fade show col-12 shadow-sm mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i> {{ session()->get('sucesso-agendamentos') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="margin-top: -200px;">
                    <div class="card-body p-4">

                        <form method="GET" action="{{ route('filaAgendamentoEspera.index') }}" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Nome</label>
                                    <input type="text" name="nome" class="form-control" value="{{ request('nome') }}"
                                        placeholder="Buscar por nome...">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Bairro</label>
                                    <input type="text" name="bairro" class="form-control"
                                        value="{{ request('bairro') }}" placeholder="Buscar por bairro...">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary">Motivo</label>
                                    <input type="text" name="motivo" class="form-control"
                                        value="{{ request('motivo') }}" placeholder="Buscar por motivo...">
                                </div>

                                <div class="col-12 text-end mt-2">
                                    <button type="submit" class="btn btn-info btn-sm text-white px-3 shadow-sm">
                                        <i class="fa fa-search me-1"></i> Pesquisar
                                    </button>
                                    <a href="{{ route('filaAgendamentoEspera.index') }}"
                                        class="btn btn-warning btn-sm px-3 shadow-sm">
                                        <i class="fa fa-eraser me-1"></i> Limpar
                                    </a>
                                </div>
                            </div>
                        </form>

                        <hr class="text-muted my-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-secondary fw-bold mb-0">Gerenciamento da Fila</h5>
                            <a class="btn btn-success btn-sm px-3 shadow-sm" href="{{ route('create-fila-agendamento') }}"
                                target="_blank">
                                <i class="fa fa-plus me-1"></i> Novo Registro na Fila
                            </a>
                        </div>

                        <div class="card shadow border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-primary text-white py-3">
                                <h4 class="mb-0 fs-5 fw-bold">
                                    <i class="fa fa-users me-2"></i> Lista de Pessoas na Fila de Espera
                                </h4>
                            </div>

                            <form id="formAgendarSelecionados" action="{{ route('fila.agendarSelecionados') }}"
                                method="POST">
                                @csrf

                                <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                    <table class="table table-sm table-bordered table-hover align-middle mb-0 text-center"
                                        style="font-size: 0.85rem;">
                                        <thead class="table-light text-secondary small fw-bold">
                                            <tr>
                                                <th width="50" class="p-2">
                                                    <input type="checkbox" id="checkAll" class="form-check-input mb-1">
                                                    <br><span class="text-uppercase"
                                                        style="font-size: 0.65rem;">Todos</span>
                                                </th>
                                                <th width="40" class="p-2">Pos.</th>
                                                <th class="text-start p-2" width="220">Cidadão / Informações</th>
                                                <th width="90" class="p-2" title="Dias restantes de Urgência">
                                                    Urgência</th>
                                                <th width="150" class="p-2">Data/Hora Atend.</th>
                                                <th class="text-start p-2" width="200">Motivo do Agendamento</th>
                                                <th class="text-start p-2" width="220">Devolutiva / Parecer Telefônico
                                                </th>
                                                <th class="text-start p-2" width="120">Bairro</th>
                                                <th width="50" class="p-2" title="Quantidade de Agendamentos">Agend.
                                                </th>
                                                <th width="50" class="p-2" title="Quantidade de Atendimentos">Atend.
                                                </th>
                                                <th width="80" class="p-2">Ações</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($FilaEspera as $agend)
                                                <tr>
                                                    <td class="p-2">
                                                        <input type="checkbox" name="selecionados[]"
                                                            value="{{ $agend->id }}"
                                                            class="form-check-input row-checkbox">
                                                    </td>
                                                    <td class="p-2 fw-bold text-primary">
                                                        {{ $FilaEspera->firstItem() + $loop->index }}
                                                    </td>
                                                    <td class="text-start p-2">
                                                        <div class="fw-bold text-dark text-uppercase"
                                                            style="font-size: 0.85rem; line-height: 1.2;">
                                                            {{ $agend->pessoa->nome }}
                                                        </div>
                                                        <div class="d-flex flex-wrap gap-2 mt-1"
                                                            style="font-size: 0.75rem;">
                                                            <span class="text-muted">
                                                                <i
                                                                    class="fa fa-phone me-1"></i>{{ $agend->pessoa->telefone }}
                                                            </span>
                                                            <span class="text-secondary">
                                                                <i class="fa fa-calendar-o me-1"></i>Cad:
                                                                {{ $agend->created_at->format('d/m/Y') }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="p-2">
                                                        @if (is_null($agend->dias_urgencia))
                                                            <span class="text-muted small">—</span>
                                                        @else
                                                            <span class="badge bg-danger px-2 py-1 mb-1 d-inline-block"
                                                                style="font-size: 0.75rem;">
                                                                {{ $agend->dias_urgencia }}
                                                                {{ Str::plural('dia', $agend->dias_urgencia) }}
                                                            </span>
                                                        @endif

                                                        @if (!empty($agend->arquivo_path))
                                                            <div class="mt-1">
                                                                <a href="{{ Storage::url($agend->arquivo_path) }}"
                                                                    target="_blank"
                                                                    class="btn btn-xs btn-outline-primary py-0 px-2"
                                                                    title="Visualizar Documento Anexado"
                                                                    style="font-size: 0.7rem; border-radius: 12px;">
                                                                    <i class="fa fa-paperclip me-1"></i> Doc
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="p-2">
                                                        <input type="datetime-local" name="datas[{{ $agend->id }}]"
                                                            class="form-control form-control-sm date-input py-1 px-2"
                                                            style="font-size: 0.8rem; min-width: 140px;">
                                                    </td>
                                                    <td class="text-start p-2">
                                                        <select class="form-select form-select-sm py-1 px-2"
                                                            name="motivos[{{ $agend->id }}]"
                                                            id="motivo_select_{{ $agend->id }}"
                                                            style="font-size: 0.8rem; max-width: 200px;">
                                                            @php
                                                                $opcoesValidas = [
                                                                    'Saude',
                                                                    'Estrada',
                                                                    'Assuntos da comunidade',
                                                                    'PESSOAL',
                                                                    'Outros Assuntos',
                                                                    'AJUDA D CUST',
                                                                    'PESSOAL/CAMINHÃO',
                                                                    'POÇO ARTESIANO',
                                                                    'REUNIÃO SOBRE AS BASES',
                                                                    'REUNIÃO COM OS PASTORES',
                                                                    'AGRADECIMENTO TRANSPORTE ESCOLAR',
                                                                    'REUNIÃO SOBRE CONCESSÃO/PROFESSORA',
                                                                    'PROJETOR DE FISSIOTERAPIA',
                                                                    'LICENÇA',
                                                                    'PROFESSOR DO ESPRAIADO',
                                                                    'BRINQUEDOS',
                                                                    'TEC. SEGURANÇA DO TRABALHO',
                                                                    'SOBRE O PONTO DA AVENIDA BAITÊ',
                                                                ];
                                                                $motivoAtual = isset($agend->motivo)
                                                                    ? mb_strtolower(trim($agend->motivo), 'UTF-8')
                                                                    : '';
                                                                $motivoExisteNaLista = false;
                                                                foreach ($opcoesValidas as $opcao) {
                                                                    if (
                                                                        mb_strtolower($opcao, 'UTF-8') === $motivoAtual
                                                                    ) {
                                                                        $motivoExisteNaLista = true;
                                                                        break;
                                                                    }
                                                                }
                                                            @endphp

                                                            @if (!$motivoExisteNaLista && !empty($motivoAtual))
                                                                <option value="{{ $agend->motivo }}" selected>
                                                                    {{ $agend->motivo }}</option>
                                                            @else
                                                                <option value="">Selecione...</option>
                                                            @endif

                                                            <option value="Saude" @selected($motivoAtual == 'saude' || $motivoAtual == 'saúde')>Saúde
                                                            </option>
                                                            <option value="Estrada" @selected($motivoAtual == 'estrada')>Estrada
                                                            </option>
                                                            <option value="Assuntos da comunidade"
                                                                @selected($motivoAtual == 'assuntos da comunidade')>Assuntos da comunidade</option>
                                                            <option value="PESSOAL" @selected($motivoAtual == 'pessoal')>Pessoal
                                                            </option>
                                                            <option value="Outros Assuntos" @selected($motivoAtual == 'outros assuntos')>
                                                                Outros Assuntos</option>
                                                            <option value="AJUDA D CUST" @selected($motivoAtual == 'ajuda d cust' || $motivoAtual == 'ajuda de custo')>Ajuda
                                                                de Custo</option>
                                                            <option value="PESSOAL/CAMINHÃO" @selected($motivoAtual == 'pessoal/caminhão')>
                                                                Pessoal/Caminhão</option>
                                                            <option value="POÇO ARTESIANO" @selected($motivoAtual == 'poço artesiano')>
                                                                Poço Artesiano</option>
                                                            <option value="REUNIÃO SOBRE AS BASES"
                                                                @selected($motivoAtual == 'reunião sobre as bases')>Reunião sobre as Bases</option>
                                                            <option value="REUNIÃO COM OS PASTORES"
                                                                @selected($motivoAtual == 'reunião com os pastores')>Reunião com os Pastores
                                                            </option>
                                                            <option value="AGRADECIMENTO TRANSPORTE ESCOLAR"
                                                                @selected($motivoAtual == 'agradecimento transporte escolar')>Agradecimento Transporte
                                                                Escolar</option>
                                                            <option value="REUNIÃO SOBRE CONCESSÃO/PROFESSORA"
                                                                @selected($motivoAtual == 'reunião sobre concessão/professora')>Reunião sobre
                                                                Concessão/Professora</option>
                                                            <option value="PROJETOR DE FISSIOTERAPIA"
                                                                @selected($motivoAtual == 'projetor de fisioterapia')>Projetor de Fisioterapia
                                                            </option>
                                                            <option value="LICENÇA" @selected($motivoAtual == 'licença')>Licença
                                                            </option>
                                                            <option value="PROFESSOR DO ESPRAIADO"
                                                                @selected($motivoAtual == 'professor do espraiado')>Professor do Espraiado
                                                            </option>
                                                            <option value="BRINQUEDOS" @selected($motivoAtual == 'brinquedos')>
                                                                Brinquedos</option>
                                                            <option value="TEC. SEGURANÇA DO TRABALHO"
                                                                @selected($motivoAtual == 'tec. segurança do trabalho')>Téc. Segurança do Trabalho
                                                            </option>
                                                            <option value="SOBRE O PONTO DA AVENIDA BAITÊ"
                                                                @selected($motivoAtual == 'sobre o ponto da avenida baitê' || $motivoAtual == 'sobre o ponto da av. baitê')>Sobre o Ponto da Avenida Baitê
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td class="p-2">
                                                        {{-- Alterado name para 'pareceres_telefonicos' para combinar com a nova estrutura e adicionado readonly --}}
                                                        <textarea data-id="{{ $agend->id }}" name="pareceres_telefonicos[{{ $agend->id }}]"
                                                            class="form-control form-control-sm parecer-input" rows="1" placeholder="Ex: Confirmou por telefone..."
                                                            style="font-size: 0.8rem; min-width: 180px;">{{ $agend->parecer_telefonico }}</textarea>
                                                    </td>
                                                    <td class="text-start p-2 text-muted text-capitalize"
                                                        style="font-size: 0.8rem;">
                                                        {{ $agend->pessoa->endereco->bairro ?? 'Não informado' }}
                                                    </td>
                                                    <td class="p-2 fw-semibold text-secondary">
                                                        {{ $agend->pessoa->agendamentos_count ?? 0 }}</td>
                                                    <td class="p-2 fw-semibold text-secondary">
                                                        {{ $agend->pessoa->atendimentos_count ?? 0 }}</td>
                                                    <td class="p-2">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <a href="{{ route('filaAgendamentoEspera.edit', $agend->id) }}"
                                                                class="btn btn-xs btn-outline-success px-2 py-0"
                                                                title="Editar" style="font-size: 0.75rem;">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                            <form action="{{ route('fila-delete-espera', $agend->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Tem certeza que deseja remover?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-xs btn-outline-danger px-2 py-0"
                                                                    title="Remover" style="font-size: 0.75rem;">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="11" class="text-center text-muted py-4">
                                                        <i class="fa fa-info-circle me-1"></i> Nenhum registro encontrado
                                                        na fila de espera.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div
                                    class="p-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        {{ $FilaEspera->withQueryString()->links() }}
                                    </div>
                                    <button type="submit" class="btn btn-primary shadow-sm">
                                        <i class="fa fa-calendar-check-o me-1"></i> Agendar Selecionados
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    
        document.querySelectorAll('.parecer-input').forEach(textarea => {

            textarea.addEventListener('blur', function() {

                let id = this.dataset.id;
                let parecer = this.value;

                fetch(`/fila-espera/atualizar-parecer/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).getAttribute('content')
                        },
                        body: JSON.stringify({
                            parecer_telefonico: parecer
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Parecer salvo');
                    })
                    .catch(error => {
                        console.error('Erro ao salvar parecer:', error);
                    });

            });

        });
        document.addEventListener('DOMContentLoaded', function() {
            const formAgendar = document.getElementById('formAgendarSelecionados');
            const checkAll = document.getElementById('checkAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');

            function inicializarInputs() {
                checkboxes.forEach(cb => {
                    atualizarEstadoLinha(cb);
                });
            }

            function atualizarEstadoLinha(checkbox) {
                const id = checkbox.value;
                const dateInput = document.querySelector(`input[name="datas[${id}]"]`);
                const motivoSelect = document.querySelector(`select[name="motivos[${id}]"]`);
                // Seletor atualizado para bater com o novo nome da textarea
                const parecerInput = document.querySelector(`textarea[name="pareceres_telefonicos[${id}]"]`);

                if (dateInput) {
                    dateInput.required = checkbox.checked;
                    dateInput.disabled = !checkbox.checked;

                    if (checkbox.checked) {
                        dateInput.classList.add('border-primary');
                    } else {
                        dateInput.classList.remove('border-primary', 'is-invalid');
                    }
                }

                if (motivoSelect) {
                    motivoSelect.disabled = !checkbox.checked;
                }

                // Controla a digitação baseado no checkbox (Readonly em vez de Disabled)
                if (parecerInput) {
                    parecerInput.readOnly = !checkbox.checked;

                    if (checkbox.checked) {
                        parecerInput.classList.add('border-primary');
                    } else {
                        parecerInput.classList.remove('border-primary');
                    }
                }
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    atualizarEstadoLinha(this);

                    if (!this.checked && checkAll) {
                        checkAll.checked = false;
                    }

                    if (checkAll && document.querySelectorAll('.row-checkbox:checked').length ===
                        checkboxes.length) {
                        checkAll.checked = true;
                    }
                });
            });

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    checkboxes.forEach(cb => {
                        cb.checked = isChecked;
                        atualizarEstadoLinha(cb);
                    });
                });
            }

            if (formAgendar) {
                formAgendar.addEventListener('submit', function(e) {
                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');

                    if (checkedBoxes.length === 0) {
                        e.preventDefault();
                        alert('Por favor, selecione pelo menos uma pessoa para realizar o agendamento.');
                        return false;
                    }

                    let invalid = false;
                    checkedBoxes.forEach(cb => {
                        const id = cb.value;
                        const dateInput = document.querySelector(`input[name="datas[${id}]"]`);

                        if (!dateInput || !dateInput.value) {
                            invalid = true;
                            dateInput.classList.add('is-invalid');
                        } else {
                            dateInput.classList.remove('is-invalid');
                        }
                    });

                    if (invalid) {
                        e.preventDefault();
                        alert(
                            'Você marcou uma ou mais linhas sem informar a data/hora. Por favor, preencha os campos destacados.'
                            );
                        return false;
                    }
                });
            }

            inicializarInputs();
        });
    </script>
@endsection
