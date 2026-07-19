@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container">
    <h2 class="mb-4">{{ isset($pagamento) ? 'Editar Pagamento' : 'Cadastrar Pagamento' }}</h2>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ isset($pagamento) ? route('pagamento.update', $pagamento->id) : route('pagamento.store') }}">
                @csrf
                @isset($pagamento)
                    @method('PUT')
                @endisset

                {{-- Dados do paciente e orçamento --}}
                <div class="mb-3">
                    <h5>Orçamento de {{ $orcamento->paciente->nome }} {{ $orcamento->paciente->sobrenome }} <b>CPF: {{ $orcamento->paciente->cpf }}</b></h5>
                </div>

                {{-- Campo oculto com o ID do orçamento --}}
                <input type="hidden" name="orcamento_paciente_id" value="{{ $orcamento->id }}">

                {{-- Valor total do orçamento --}}
                <div class="mb-3">
                    <label class="form-label">Valor Total do Orçamento</label>
                    <input type="text" class="form-control bg-light"
                           value="R$ {{ number_format($orcamento->valor_liquido ?? $orcamento->total_orcamento, 2, ',', '.') }}" disabled>
                    <small class="text-muted">Usamos o valor líquido se houver desconto</small>
                </div>

                {{-- Total já pago (PAGO + PENDENTE, conforme regra) --}}
                @php
                    $valorBase = $orcamento->valor_liquido ?? $orcamento->total_orcamento;
                    $totalPago = $orcamento->pagamentos->whereIn('status_pagamento', ['PAGO', 'PENDENTE'])->sum('valor_pago');
                    // Na edição, desconsidera o valor atual para cálculo correto
                    if (isset($pagamento)) {
                        $totalPago = $totalPago - $pagamento->valor_pago;
                    }
                    $saldoRestante = $valorBase - $totalPago;
                @endphp

                <div class="mb-3">
                    <label class="form-label">Total Já Pago</label>
                    <input type="text" class="form-control bg-light"
                           value="R$ {{ number_format($totalPago, 2, ',', '.') }}" disabled>
                </div>

                {{-- Saldo Restante --}}
                <div class="mb-3">
                    <label class="form-label">Saldo Restante</label>
                    <input type="text" class="form-control bg-light" id="saldo_restante"
                           value="R$ {{ number_format($saldoRestante, 2, ',', '.') }}" disabled>
                </div>

                {{-- Valor deste Pagamento --}}
                <div class="mb-3">
                    <label for="valor_pago" class="form-label">Valor deste Pagamento *</label>
                    <input type="number" name="valor_pago" id="valor_pago" step="0.01" min="0.01" max="{{ $saldoRestante }}"
                           class="form-control @error('valor_pago') is-invalid @enderror"
                           value="{{ old('valor_pago', $pagamento->valor_pago ?? '') }}" required>
                    @error('valor_pago')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Forma de Pagamento --}}
                <div class="mb-3">
                    <label for="forma_pagamento" class="form-label">Forma de Pagamento *</label>
                    <input type="text" name="forma_pagamento" id="forma_pagamento" maxlength="45"
                           class="form-control @error('forma_pagamento') is-invalid @enderror"
                           value="{{ old('forma_pagamento', $pagamento->forma_pagamento ?? '') }}" required>
                    <small class="text-muted">Ex: Pix, Cartão, Dinheiro, Boleto</small>
                    @error('forma_pagamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status do Pagamento --}}
                <div class="mb-3">
                    <label for="status_pagamento" class="form-label">Status do Pagamento *</label>
                    <select name="status_pagamento" id="status_pagamento" class="form-select @error('status_pagamento') is-invalid @enderror" required>
                        <option value="PENDENTE" {{ old('status_pagamento', $pagamento->status_pagamento ?? 'PENDENTE') == 'PENDENTE' ? 'selected' : '' }}>PENDENTE</option>
                        <option value="PAGO" {{ old('status_pagamento', $pagamento->status_pagamento ?? '') == 'PAGO' ? 'selected' : '' }}>PAGO</option>
                        <option value="CANCELADO" {{ old('status_pagamento', $pagamento->status_pagamento ?? '') == 'CANCELADO' ? 'selected' : '' }}>CANCELADO</option>
                        <option value="ESTORNADO" {{ old('status_pagamento', $pagamento->status_pagamento ?? '') == 'ESTORNADO' ? 'selected' : '' }}>ESTORNADO</option>
                    </select>
                    @error('status_pagamento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Observação --}}
                <div class="mb-3">
                    <label for="observacao" class="form-label">Observação</label>
                    <textarea name="observacao" id="observacao" rows="3" maxlength="255"
                              class="form-control @error('observacao') is-invalid @enderror">{{ old('observacao', $pagamento->observacao ?? '') }}</textarea>
                    @error('observacao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Botões --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="{{ route('pagamento.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script atualizado --}}
<script>
    const valorBase = {{ $orcamento->valor_liquido ?? $orcamento->total_orcamento }};
    let totalPagoFixado = {{ $totalPago }};
    const inputValorPago = document.getElementById('valor_pago');
    const inputSaldo = document.getElementById('saldo_restante');

    function atualizarSaldoTela() {
        const valorInformado = parseFloat(inputValorPago.value) || 0;
        const saldoCalculado = valorBase - totalPagoFixado - valorInformado;
        inputSaldo.value = `R$ ${saldoCalculado.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
    }

    // Atualiza ao carregar e ao digitar
    atualizarSaldoTela();
    inputValorPago.addEventListener('input', atualizarSaldoTela);
</script>
@endsection