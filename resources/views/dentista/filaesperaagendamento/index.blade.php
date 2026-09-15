@extends('dentista.layout.paginaDentista')

@section('pagina')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid py-3">

    @if (session()->has('sucesso-agendamentos'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session()->get('sucesso-agendamentos') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4">

            {{-- Área de Busca --}}
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
                        <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                            <i class="fa fa-search me-1"></i> Pesquisar
                        </button>
                        <a href="{{ route('filaAgendamentoEspera.index') }}"
                           class="btn btn-outline-secondary btn-sm shadow-sm">
                            <i class="fa fa-eraser me-1"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>

            <hr class="text-muted my-4">

            {{-- Cabeçalho da Lista --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-secondary fw-bold mb-0">Gerenciamento da Fila</h5>
                <a class="btn btn-success btn-sm shadow-sm" href="{{ route('filaAgendamentoEspera.create') }}">
                    <i class="fa fa-plus me-1"></i> Novo Registro na Fila
                </a>
            </div>

            {{-- Tabela Principal --}}
            <div class="card shadow border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0 fs-5 fw-bold">
                        <i class="fa fa-users me-2"></i> Lista de Pessoas na Fila de Espera
                    </h4>
                </div>

                <form id="formAgendarSelecionados" action="{{ route('fila.agendarSelecionados') }}" method="POST">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
                            <thead class="table-light text-secondary small fw-bold">
                                <tr>
                                    <th width="50" class="p-2">
                                        <input type="checkbox" id="checkAll" class="form-check-input mb-1">
                                        <br><span class="text-uppercase" style="font-size: 0.65rem;">Todos</span>
                                    </th>
                                    <th width="40" class="p-2">Pos.</th>
                                    <th class="text-start p-2" width="220">Cidadão / Informações</th>
                                    <th width="90" class="p-2" title="Dias restantes de Urgência">Urgência</th>
                                    <th width="150" class="p-2">Data/Hora Atend.</th>
                                    <th class="text-start p-2" width="200">Motivo do Agendamento</th>
                                    <th class="text-start p-2" width="220">Devolutiva / Parecer Telefônico</th>
                                    <th class="text-start p-2" width="120">Bairro</th>
                                    <th width="50" class="p-2" title="Quantidade de Agendamentos">Agend.</th>
                                    <th width="50" class="p-2" title="Quantidade de Atendimentos">Atend.</th>
                                    <th width="80" class="p-2">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($filas as $agend)
                                <tr>
                                    <td class="p-2">
                                        <input type="checkbox" name="selecionados[]" value="{{ $agend->id }}" class="form-check-input row-checkbox">
                                    </td>
                                    <td class="p-2 fw-semibold text-secondary">
                                        {{ $loop->iteration + ($filas->currentPage() - 1) * $filas->perPage() }}
                                    </td>
                                    <td class="text-start p-2">
                                        <div class="fw-bold text-dark text-uppercase" style="font-size: 0.85rem; line-height: 1.2;">
                                            {{ $agend->paciente->nome ?? $agend->pessoa->nome ?? 'Não informado' }}
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mt-1" style="font-size: 0.75rem;">
                                            <span class="text-muted">
                                                <i class="fa fa-phone me-1"></i>{{ $agend->paciente->telefone ?? $agend->pessoa->telefone ?? '—' }}
                                            </span>
                                            <span class="text-secondary">
                                                <i class="fa fa-calendar-o me-1"></i>Cad: {{ $agend->created_at?->format('d/m/Y') ?? '—' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        @if (is_null($agend->dias_urgencia))
                                            <span class="text-muted small">—</span>
                                        @else
                                            <span class="badge bg-danger px-2 py-1" style="font-size: 0.75rem;">
                                                {{ $agend->dias_urgencia }} {{ Str::plural('dia', $agend->dias_urgencia) }}
                                            </span>
                                        @endif
                                        @if (!empty($agend->arquivo_path))
                                            <div class="mt-1">
                                                <a href="{{ Storage::url($agend->arquivo_path) }}" target="_blank"
                                                   class="btn btn-xs btn-outline-primary py-0 px-2" title="Visualizar Documento"
                                                   style="font-size: 0.7rem; border-radius: 12px;">
                                                    <i class="fa fa-paperclip me-1"></i> Doc
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-2">
                                        <input type="datetime-local" name="datas[{{ $agend->id }}]"
                                               class="form-control form-control-sm py-1 px-2" style="font-size: 0.8rem; min-width: 140px;">
                                    </td>
                                    <td class="text-start p-2">
                                        {{-- Apenas input de texto, sem opções --}}
                                        <input type="text" name="motivos[{{ $agend->id }}]"
                                               class="form-control form-control-sm py-1 px-2"
                                               style="font-size: 0.8rem; max-width: 200px;"
                                               value="{{ $agend->motivo ?? '' }}"
                                               placeholder="Motivo...">
                                    </td>
                                    <td class="p-2">
                                        <textarea data-id="{{ $agend->id }}" name="pareceres_telefonicos[{{ $agend->id }}]"
                                                  class="form-control form-control-sm parecer-input" rows="1"
                                                  placeholder="Ex: Confirmou por telefone..."
                                                  style="font-size: 0.8rem; min-width: 180px;">{{ $agend->parecer_telefonico ?? '' }}</textarea>
                                    </td>
                                    <td class="text-start p-2 text-muted text-capitalize" style="font-size: 0.8rem;">
                                        {{ $agend->pessoa->endereco->bairro ?? $agend->paciente->bairro ?? 'Não informado' }}
                                    </td>
                                    <td class="p-2 fw-semibold text-secondary">{{ $agend->pessoa->agendamentos_count ?? 0 }}</td>
                                    <td class="p-2 fw-semibold text-secondary">{{ $agend->pessoa->atendimentos_count ?? 0 }}</td>
                                    <td class="p-2">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('filaAgendamentoEspera.edit', $agend->id) }}"
                                               class="btn btn-xs btn-outline-success px-2 py-0" title="Editar" style="font-size: 0.75rem;">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <form action="{{ route('fila-delete-espera', $agend->id) }}" method="POST"
                                                  onsubmit="return confirm('Tem certeza que deseja remover este registro?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger px-2 py-0" title="Remover" style="font-size: 0.75rem;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="fa fa-info-circle me-1"></i> Nenhum registro encontrado na fila de espera.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            @if ($filas->hasPages())
                                {{ $filas->appends(request()->query())->links('pagination::bootstrap-5') }}
                            @endif
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

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let debounceTimer;

function salvarParecer(inputElement) {
    const id = inputElement.dataset.id;
    const valor = inputElement.value;
    inputElement.classList.add('border-warning');

    fetch(`/fila-espera/atualizar-parecer/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({ parecer_telefonico: valor })
    })
    .then(res => res.json())
    .then(() => {
        inputElement.classList.replace('border-warning', 'border-success');
        setTimeout(() => inputElement.classList.remove('border-success'), 2000);
    })
    .catch(err => {
        console.error(err);
        inputElement.classList.remove('border-warning');
        alert('Erro ao salvar parecer.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('table').addEventListener('input', e => {
        if (e.target.classList.contains('parecer-input')) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => salvarParecer(e.target), 600);
        }
    });

    document.getElementById('checkAll').addEventListener('change', e => {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
    });

    document.getElementById('formAgendarSelecionados').addEventListener('submit', function(e) {
        const selecionados = document.querySelectorAll('.row-checkbox:checked');
        if (!selecionados.length) {
            e.preventDefault();
            alert('Selecione ao menos um registro.');
            return;
        }
        const btn = this.querySelector('[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Processando...';
    });
});
</script>
@endsection