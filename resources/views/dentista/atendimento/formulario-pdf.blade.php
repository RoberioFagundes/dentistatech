<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atendimento #{{ $atendimento->id }} - DentistaTech</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 14px; 
            line-height: 1.6; 
            padding: 30px;
            color: #333;
        }
        .cabecalho { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #0d6efd; 
            padding-bottom: 15px; 
        }
        .cabecalho h2 { color: #0d6efd; font-size: 22px; margin-bottom: 5px; }
        .cabecalho h3 { font-size: 16px; font-weight: normal; color: #555; }
        .secao { margin-bottom: 25px; }
        .titulo-secao { 
            font-weight: bold; 
            color: #0d6efd; 
            margin-bottom: 12px; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 5px;
            font-size: 16px;
        }
        .linha { margin-bottom: 8px; }
        .rotulo { font-weight: bold; display: inline-block; width: 180px; color: #444; }
        .valor { color: #222; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            border: 1px solid #ddd;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
            font-size: 13px;
        }
        th { background-color: #f8f9fa; font-weight: bold; color: #0d6efd; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .sem-registro { color: #888; font-style: italic; padding: 10px 0; }
        .caixa-pagamento {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #198754;
        }
        .rodape {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>

<!-- Cabeçalho -->
<div class="cabecalho">
    <h2>DENTISTATECH</h2>
    <h3>REGISTRO DE ATENDIMENTO Nº {{ $atendimento->id }}</h3>
    <p style="font-size: 12px; margin-top: 5px;">Gerado em {{ now()->format('d/m/Y H:i') }}</p>
</div>

<!-- Dados Gerais -->
<div class="secao">
    <div class="titulo-secao">Dados Gerais</div>
    <div class="linha">
        <span class="rotulo">Paciente:</span>
        <span class="valor">{{ $atendimento->paciente->nome ?? 'Não informado' }}</span>
    </div>
    <div class="linha">
        <span class="rotulo">Dentista Responsável:</span>
        <span class="valor">{{ $atendimento->dentista->nome ?? 'Não informado' }}</span>
    </div>
    <div class="linha">
        <span class="rotulo">Data do Atendimento:</span>
        <span class="valor">{{ $atendimento->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</span>
    </div>
    @if($atendimento->agendamento_id)
    <div class="linha">
        <span class="rotulo">Agendamento Vinculado:</span>
        <span class="valor">#{{ $atendimento->agendamento_id }}</span>
    </div>
    @endif
</div>

<!-- Dados Clínicos -->
<div class="secao">
    <div class="titulo-secao">Dados Clínicos</div>
    <div class="linha">
        <span class="rotulo">Queixa Principal:</span>
        <span class="valor">{{ $atendimento->queixa_principal ?? 'Não informada' }}</span>
    </div>
    <div class="linha">
        <span class="rotulo">Diagnóstico:</span>
        <span class="valor">{{ $atendimento->diagnostico ?? 'Não informado' }}</span>
    </div>
    <div class="linha">
        <span class="rotulo">Tratamento Executado:</span>
        <span class="valor">{{ $atendimento->tratamento_executado ?? 'Não informado' }}</span>
    </div>
    <div class="linha">
        <span class="rotulo">Observações Clínicas:</span>
        <span class="valor">{{ $atendimento->observacoes_clinicas ?? 'Não informadas' }}</span>
    </div>
</div>

<!-- Procedimentos Realizados -->
<div class="secao">
    <div class="titulo-secao">Procedimentos Realizados</div>
    @if($atendimento->procedimentos->count() > 0)
    <table>
        <thead>
            <tr>
                <th width="15%">Dente</th>
                <th width="45%">Procedimento</th>
                <th width="20%" class="text-right">Valor Unitário</th>
                <th width="20%">Observação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atendimento->procedimentos as $proc)
            <tr>
                <td class="text-center">{{ $proc->pivot->numero_dente ?? '—' }}</td>
                <td>{{ $proc->nome ?? 'Procedimento não encontrado' }}</td>
                <td class="text-right">R$ {{ number_format($proc->valor ?? 0, 2, ',', '.') }}</td>
                <td>{{ $proc->pivot->observacao ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="sem-registro">Nenhum procedimento foi vinculado a este atendimento.</p>
    @endif
</div>

<!-- Dados do Orçamento -->
@if($atendimento->orcamento)
<div class="secao">
    <div class="titulo-secao">Orçamento</div>
    <div class="linha">
        <span class="rotulo">Valor Total:</span>
        <span class="valor">R$ {{ number_format($atendimento->orcamento->total_orcamento ?? 0, 2, ',', '.') }}</span>
    </div>
    <div class="linha">
        <span class="rotulo">Desconto Aplicado:</span>
        <span class="valor">R$ {{ number_format($atendimento->orcamento->desconto ?? 0, 2, ',', '.') }}</span>
    </div>
    <div class="linha">
        <span class="rotulo">Valor Líquido:</span>
        <span class="valor"><strong>R$ {{ number_format(($atendimento->orcamento->total_orcamento ?? 0) - ($atendimento->orcamento->desconto ?? 0), 2, ',', '.') }}</strong></span>
    </div>
    <div class="linha">
        <span class="rotulo">Validade do Orçamento:</span>
        <span class="valor">{{ $atendimento->orcamento->validade ? \Carbon\Carbon::parse($atendimento->orcamento->validade)->format('d/m/Y') : 'Não informada' }}</span>
    </div>
</div>
@endif

<!-- Dados de Pagamento (CORRIGIDO) -->
<div class="secao">
    <div class="titulo-secao">Dados de Pagamento</div>

    @php
        // ✅ Carrega o último pagamento vinculado ao orçamento do atendimento
        $ultimoPagamento = $atendimento->orcamento?->pagamentos?->last();
    @endphp

    @if($ultimoPagamento)
    <div class="caixa-pagamento">
        <div class="linha">
            <span class="rotulo">Valor Pago:</span>
            <span class="valor">R$ {{ number_format($ultimoPagamento->valor_pago ?? 0, 2, ',', '.') }}</span>
        </div>
        <div class="linha">
            <span class="rotulo">Forma de Pagamento:</span>
            <span class="valor">{{ $ultimoPagamento->forma_pagamento ?? 'Não informada' }}</span>
        </div>
        <div class="linha">
            <span class="rotulo">Status do Pagamento:</span>
            <span class="valor">{{ $ultimoPagamento->status_pagamento ?? 'Não informado' }}</span>
        </div>
        <div class="linha">
            <span class="rotulo">Data do Pagamento:</span>
            <span class="valor">{{ $ultimoPagamento->created_at ? \Carbon\Carbon::parse($ultimoPagamento->data_pagamento)->format('d/m/Y') : 'Não informada' }}</span>
        </div>
    </div>
    @else
    <p class="sem-registro">Nenhum registro de pagamento vinculado a este atendimento.</p>
    @endif
</div>

<!-- Rodapé -->
<div class="rodape">
    <p>Documento emitido pelo sistema DentistaTech - CNPJ XX.XXX.XXX/0001-XX</p>
    <p>Este documento é válido apenas para registro interno e acompanhamento clínico.</p>
</div>

</body>
</html>