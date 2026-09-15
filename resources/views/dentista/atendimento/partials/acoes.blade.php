@if($isMobile)
    {{-- Ações para Dispositivos Móveis --}}
    @if(!$atendimento->orcamento)
        <form action="{{ route('pagamento.create', $atendimento->id) }}" method="POST" class="flex-grow-1">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-success w-100">Gerar Orçamento</button>
        </form>
    @else
        <a href="{{ route('pagamento.create', $atendimento->orcamento->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1">Pagar Orçamento</a>
    @endif

    <a href="{{ route('atendimento.pdf', $atendimento->id) }}" target="_blank" class="btn btn-sm btn-outline-primary flex-grow-1">PDF</a>
    <a href="{{ route('atendimento.edit', $atendimento->id) }}" class="btn btn-sm btn-outline-secondary flex-grow-1">Editar</a>
    
    <form action="{{ route('atendimento.destroy', $atendimento->id) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Excluir este atendimento permanentemente?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Excluir</button>
    </form>

@else
    {{-- Ações para Tabela (Desktop) --}}
    <div class="btn-group btn-group-sm" role="group">
        {{-- Gerar orçamento (se não existir) --}}
        @if(!$atendimento->orcamento)
        <form action="{{ route('pagamento.create', $atendimento->id) }}" method="POST" class="d-inline m-0">
            @csrf
            <button type="submit" class="btn btn-outline-success rounded-0 rounded-start" title="Gerar orçamento para este atendimento">
                <i class="bi bi-money"></i>
            </button>
        </form>
        @endif

        {{-- Gerar PDF do atendimento --}}
        <a href="{{ route('atendimento.pdf', $atendimento->id) }}" class="btn btn-outline-primary" title="Gerar PDF do atendimento">
            <i class="bi bi-file-earmark-pdf"></i>
        </a>

        {{-- Pagar / Visualizar pagamento do orçamento (se já existir) --}}
        @if($atendimento->orcamento)
        <a href="{{ route('pagamento.create', $atendimento->orcamento->id) }}" class="btn btn-outline-primary" title="Pagar orçamento">
            <i class="bi bi-cash-coin"></i>
        </a>
        @endif

        {{-- Editar atendimento --}}
        <a href="{{ route('atendimento.edit', $atendimento->id) }}" class="btn btn-outline-secondary" title="Editar atendimento">
            <i class="bi bi-pencil"></i>
        </a>

        {{-- Excluir atendimento --}}
        <form action="{{ route('atendimento.destroy', $atendimento->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Excluir este atendimento permanentemente?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger rounded-0 rounded-end" title="Excluir atendimento">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
@endif