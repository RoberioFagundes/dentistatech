<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Atendimento #{{ $atendimento->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5; padding: 20px; }
        .cabecalho { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0d6efd; padding-bottom: 10px; }
        .secao { margin-bottom: 20px; }
        .titulo-secao { font-weight: bold; color: #0d6efd; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .linha { margin-bottom: 5px; }
        .rotulo { font-weight: bold; display: inline-block; width: 180px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="cabecalho">
    <h2>DENTISTATECH</h2>
    <h3>REGISTRO DE ATENDIMENTO #{{ $atendimento->id }}</h3>
</div>

<div class="secao">
    <div class="titulo-secao">Dados Gerais</div>
    <div class="linha"><span class="rotulo">Paciente:</span> {{ $atendimento->paciente->nome ?? 'Não informado' }}</div>
    <div class="linha"><span class="rotulo">Dentista:</span> {{ $atendimento->dentista->nome ?? 'Não informado' }}</div>
    <div class="linha"><span class="rotulo">Data:</span> {{ now()->format('d/m/Y H:i') }}</div>
</div>

<div class="secao">
    <div class="titulo-secao">Dados Clínicos</div>
    <div class="linha"><span class="rotulo">Queixa Principal:</span> {{ $atendimento->queixa_principal }}</div>
    <div class="linha"><span class="rotulo">Diagnóstico:</span><br>{{ $atendimento->diagnostico ?? '—' }}</div>
    <div class="linha"><span class="rotulo">Tratamento Executado:</span><br>{{ $atendimento->tratamento_executado ?? '—' }}</div>
    <div class="linha"><span class="rotulo">Observações Clínicas:</span><br>{{ $atendimento->observacoes_clinicas ?? '—' }}</div>
</div>

<div class="secao">
    <div class="titulo-secao">Procedimentos Realizados</div>
    @if($atendimento->procedimentos->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Dente</th>
                <th>Procedimento</th>
                <th>Valor</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atendimento->procedimentos as $proc)
            <tr>
                <td>{{ $proc->pivot->numero_dente }}</td>
                <td>{{ $proc->nome }}</td>
                <td>R$ {{ number_format($proc->valor, 2, ',', '.') }}</td>
                <td>{{ $proc->pivot->observacao ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Nenhum procedimento vinculado.</p>
    @endif
</div>

<div class="secao">
    <div class="titulo-secao">Orçamento</div>
    <div class="linha"><span class="rotulo">Valor Total:</span> R$ {{ number_format($atendimento->orcamento->total_orcamento ?? 0, 2, ',', '.') }}</div>
    <div class="linha"><span class="rotulo">Desconto:</span> R$ {{ number_format($atendimento->orcamento->desconto ?? 0, 2, ',', '.') }}</div>
    <div class="linha"><span class="rotulo">Validade:</span> {{ $atendimento->orcamento->validade ? \Carbon\Carbon::parse($atendimento->orcamento->validade)->format('d/m/Y') : 'Não informada' }}</div>
</div>

</body>
</html>