<style>
    :root{
        --sidebar-bg:#ffffff;
        --sidebar-border:#e9ecef;
        --sidebar-hover:#f4f8ff;
        --sidebar-active:#0d6efd;
        --sidebar-active-light:#e8f1ff;
        --sidebar-text:#495057;
        --sidebar-muted:#6c757d;
    }

    .patch-sidebar{
        display:block;
        max-height:calc(100vh - 90px);
        overflow-y:auto;
        overflow-x:hidden;
        padding:8px;
        scrollbar-width:thin;
        scrollbar-color:#b9c6da transparent;
    }

    .patch-sidebar::-webkit-scrollbar{ width:6px; }
    .patch-sidebar::-webkit-scrollbar-thumb{ background:#c7d2e2; border-radius:20px; }
    .patch-sidebar::-webkit-scrollbar-thumb:hover{ background:#9fb5d8; }

    .nav-small-cap{
        margin-top:18px; margin-bottom:8px; padding:0 14px;
        font-size:.72rem; color:#8b95a5!important;
        font-weight:700; letter-spacing:1.5px; text-transform:uppercase;
    }

    .sidebar-link{
        display:flex; align-items:center; gap:14px; padding:12px 14px;
        border-radius:12px; color:var(--sidebar-text)!important;
        transition:.25s; position:relative; text-decoration:none;
    }

    .sidebar-link:hover{ background:var(--sidebar-hover); color:var(--sidebar-active)!important; }

    .sidebar-link.active{
        background:linear-gradient(90deg,#0d6efd,#4b8dff)!important;
        color:#fff!important;
        box-shadow:0 8px 22px rgba(13,110,253,.22);
    }
</style>

<ul id="sidebarnav" class="nav flex-column patch-sidebar" style="margin-top:-160px">
    
    <li class="nav-small-cap mt-2 mb-1">
        <span class="hide-menu ms-1">Home</span>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2 fs-5"></i>
            <span class="hide-menu fw-medium">Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
            <i class="bi bi-person-gear fs-5"></i>
            <span class="hide-menu fw-medium">Meu Perfil</span>
        </a>
    </li>

    @php
        $menuItems = [
            'Administrativo' => [
                ['route' => 'financeiro.index', 'icon' => 'bi-people-fill', 'label' => 'Funcionários', 'pattern' => 'financeiro.*']
            ],
            'Financeiro' => [
                ['route' => 'financeiro.index', 'icon' => 'bi-person-badge', 'label' => 'Pagamentos', 'pattern' => 'financeiro.*'],
                ['route' => 'financeiro.index', 'icon' => 'bi-person-badge', 'label' => 'Orçamentos', 'pattern' => 'financeiro.*']
            ],
            'Serviços' => [
                ['route' => 'dentista.index', 'icon' => 'bi-person-badge', 'label' => 'Dentistas', 'pattern' => 'dentista.*'],
                ['route' => 'procedimento.index', 'icon' => 'bi-capsule', 'label' => 'Procedimentos', 'pattern' => 'procedimento.*'],
                ['route' => 'paciente.index', 'icon' => 'bi-heart-pulse', 'label' => 'Pacientes', 'pattern' => 'paciente.*'],
                ['route' => 'agendamento.index', 'icon' => 'bi-calendar-event', 'label' => 'Agendamentos', 'pattern' => 'agendamento.*'],
                ['route' => 'filaAtendimento.index', 'icon' => 'bi-person-lines-fill', 'label' => 'Fila de Atendimentos', 'pattern' => 'filaAtendimento.*'],
                ['route' => 'atendimento.index', 'icon' => 'bi-clipboard2-check', 'label' => 'Atendimentos', 'pattern' => 'atendimento.*'],
                ['route' => 'filaAgendamentoEspera.create', 'icon' => 'bi-hourglass-split', 'label' => 'Fila de Espera', 'pattern' => 'filaAgendamentoEspera.*'],
                ['route' => 'remarcacao.index', 'icon' => 'bi-calendar-range', 'label' => 'Remarcação', 'pattern' => 'remarcacao.*']
            ]
        ];
    @endphp

    @foreach($menuItems as $category => $links)
        <li class="nav-small-cap mt-3 mb-1">{{ $category }}</li>
        @foreach($links as $link)
           
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs($link['pattern']) ? 'active' : '' }}" href="{{ route($link['route']) }}">
                        <i class="bi {{ $link['icon'] }} fs-5"></i>
                        <span class="hide-menu fw-medium">{{ $link['label'] }}</span>
                    </a>
                </li>
         
        @endforeach
    @endforeach
</ul>