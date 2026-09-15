@extends('dentista.layout.paginaDentista')

@section('pagina')

<style>
    .dashboard-cards {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        padding: 0 0 24px 0;
        font-family: Arial, sans-serif;
    }

    .card {
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        min-width: 180px;
        flex: 1;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        border: 1px solid #f3f4f6;
    }

    .card__icon {
        background: rgba(59, 130, 246, 0.1);
        padding: 10px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .card--blue .card__icon { background: rgba(59, 130, 246, 0.1); }
    .card--green .card__icon { background: rgba(16, 185, 129, 0.1); }
    .card--purple .card__icon { background: rgba(139, 92, 246, 0.1); }
    .card--orange .card__icon { background: rgba(245, 158, 11, 0.1); }
    .card--light-green .card__icon { background: rgba(34, 197, 94, 0.1); }

    .card__content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .card__title {
        margin: 0;
        font-size: 14px;
        font-weight: 500;
        color: #4b5563;
    }

    .card__value {
        margin: 4px 0;
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }

    .card__subtitle, .card__date {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
    }

    /* Estilos para a nova seção */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-top: 24px;
    }

    @media(max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    .section-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        border: 1px solid #f3f4f6;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.4;
    }

    /* Gráfico circular */
    .chart-container {
        display: flex;
        align-items: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .donut-chart {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: conic-gradient(#dbeafe 0deg 360deg, #ffffff 360deg);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .donut-inner {
        width: 120px;
        height: 120px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .donut-total {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
    }

    .donut-label {
        font-size: 12px;
        color: #64748b;
    }

    .status-list {
        flex: 1;
        min-width: 200px;
    }

    .status-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
        font-size: 14px;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    .dot-realizado { background: #22c55e; }
    .dot-cancelado { background: #ef4444; }
    .dot-remarcado { background: #f59e0b; }
    .dot-naocompareceu { background: #8b5cf6; }

    .no-data-alert {
        margin-top: 16px;
        padding: 8px 12px;
        background: #eff6ff;
        border-radius: 6px;
        color: #1d4ed8;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .footer {
        text-align: center;
        margin-top: 32px;
        font-size: 13px;
        color: #64748b;
    }
</style>

<!-- Container principal dos cards -->
<div class="dashboard-cards">
    <!-- Card 1: Agendamentos Hoje -->
    <div class="card card--blue">
        <div class="card__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
        </div>
        <div class="card__content">
            <h3 class="card__title">Agendamentos Hoje</h3>
            <p class="card__value">{{ $agendamentosHoje ?? 0 }}</p>
            <p class="card__date">{{ \Carbon\Carbon::today()->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Card 2: Atendimentos Hoje -->
    <div class="card card--green">
        <div class="card__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <polyline points="16 11 18 13 22 9"></polyline>
            </svg>
        </div>
        <div class="card__content">
            <h3 class="card__title">Atendimentos Hoje</h3>
            <p class="card__value">{{ $atendimentosHoje ?? 0 }}</p>
            <p class="card__subtitle">Realizados</p>
        </div>
    </div>

    <!-- Card 3: Pacientes Cadastrados -->
    <div class="card card--purple">
        <div class="card__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>
        <div class="card__content">
            <h3 class="card__title">Pacientes Cadastrados</h3>
            <p class="card__value">{{ $totalPacientes ?? 0 }}</p>
            <p class="card__subtitle">Total de pacientes</p>
        </div>
    </div>

    <!-- Card 4: Orçamentos Abertos -->
    <div class="card card--orange">
        <div class="card__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </div>
        <div class="card__content">
            <h3 class="card__title">Orçamentos Abertos</h3>
            <p class="card__value">{{ $orcamentosAbertos ?? 0 }}</p>
            <p class="card__subtitle">Em aberto</p>
        </div>
    </div>

    <!-- Card 5: Recebimentos (Mês) -->
    <div class="card card--light-green">
        <div class="card__icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="6" x2="12" y2="18"></line>
                <line x1="8" y1="10" x2="16" y2="10"></line>
                <line x1="8" y1="14" x2="16" y2="14"></line>
            </svg>
        </div>
        <div class="card__content">
            <h3 class="card__title">Recebimentos (Mês)</h3>
            <p class="card__value">R$ {{ number_format($recebimentosMes ?? 0, 2, ',', '.') }}</p>
            <p class="card__subtitle">Total do mês</p>
        </div>
    </div>
</div>

<!-- Seção Dupla: Atendimentos + Resumo do Mês -->
<div class="dashboard-grid">
    <!-- Últimos Atendimentos Realizados -->
    <div class="section-card">
        <div class="section-header">
            <h3 class="section-title">
                <span>🔍</span> Últimos Atendimentos Realizados
            </h3>
            <span>⋮</span>
        </div>

        @if(isset($atendimentos) && count($atendimentos) > 0)
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr>
                        <th class="text-muted fw-medium">Data / Hora</th>
                        <th class="text-muted fw-medium">Paciente</th>
                        <th class="text-muted fw-medium">Queixa</th>
                        <th class="text-muted fw-medium">Diagnóstico</th>
                        <th class="text-muted fw-medium">Tratamento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($atendimentos as $atendimento)
                    <tr>
                        <td>{{ $atendimento->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $atendimento->paciente->nome ?? 'Não informado' }}</td>
                        <td>{{ Str::limit($atendimento->queixa_principal, 20) }}</td>
                        <td>{{ Str::limit($atendimento->diagnostico, 20) }}</td>
                        <td>{{ Str::limit($atendimento->tratamento_executado, 20) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <div class="empty-icon">📋🔎</div>
            <p>Nenhum atendimento registrado recentemente.</p>
        </div>
        @endif
    </div>

    <!-- Resumo do Mês -->
    <div class="section-card">
        <div class="section-header">
            <h3 class="section-title">
                <span>📊</span> Resumo do Mês
            </h3>
            <select class="form-select form-select-sm w-auto">
                <option>{{ \Carbon\Carbon::now()->translatedFormat('F/Y') }}</option>
                <!-- Você pode adicionar os meses anteriores dinamicamente -->
            </select>
        </div>

        <div class="chart-container">
            <!-- Gráfico Circular -->
            <div class="donut-chart">
                <div class="donut-inner">
                    <div class="donut-total">{{ $totalAtendimentosMes ?? 0 }}</div>
                    <div class="donut-label">Total de Atendimentos</div>
                </div>
            </div>

            <!-- Lista de Status -->
            <div class="status-list">
                <div class="status-item">
                    <span><span class="status-dot dot-realizado"></span>Realizados</span>
                    <span>{{ $realizados ?? 0 }} ({{ $percRealizados ?? '0' }}%)</span>
                </div>
                <div class="status-item">
                    <span><span class="status-dot dot-cancelado"></span>Cancelados</span>
                    <span>{{ $cancelados ?? 0 }} ({{ $percCancelados ?? '0' }}%)</span>
                </div>
                <div class="status-item">
                    <span><span class="status-dot dot-remarcado"></span>Remarcados</span>
                    <span>{{ $remarcados ?? 0 }} ({{ $percRemarcados ?? '0' }}%)</span>
                </div>
                <div class="status-item">
                    <span><span class="status-dot dot-naocompareceu"></span>Não Compareceram</span>
                    <span>{{ $naoCompareceram ?? 0 }} ({{ $percNaoCompareceram ?? '0' }}%)</span>
                </div>
            </div>
        </div>

        <div class="no-data-alert">
            <span>↗️</span> Nenhum atendimento registrado no período.
        </div>
    </div>
</div>

<!-- Rodapé -->
<div class="footer">
    © {{ date('Y') }} DentistaTech. Todos os direitos reservados.
</div>

@endsection