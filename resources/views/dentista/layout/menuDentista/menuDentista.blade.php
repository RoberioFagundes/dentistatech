<style>
    .left-sidebar {
        width: 270px;
        height: calc(100vh - 64px);
        position: fixed;
        top: 64px;
        left: 0;
        background: #ffffff;
        border-right: 1px solid #e9ecef;
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        z-index: 1050;
        display: flex;
        flex-direction: column;
    }

    .left-sidebar.show {
        transform: translateX(0);
    }

    .sidebar-nav-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem;
    }

    .sidebar-nav-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .sidebar-nav-scroll::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }

    .sidebar-overlay {
        position: fixed;
        top: 64px;
        left: 0;
        width: 100%;
        height: calc(100vh - 64px);
        background: rgba(0,0,0,0.4);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    @media (min-width: 992px) {
        .left-sidebar {
            transform: translateX(0);
        }
        .sidebar-overlay {
            display: none !important;
        }
    }
</style>

<aside class="left-sidebar" id="sidebarMenu">
    <div class="p-3 border-bottom text-center flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="text-decoration-none">
            <img src="{{ asset('favicon.ico') }}" alt="Perfil" width="40" height="40" class="rounded-circle object-fit-cover mb-2">
            <span class="fs-5 fw-bold text-primary">Dentista<span class="text-dark">Tech</span></span>
        </a>
    </div>

    <div class="sidebar-nav-scroll">
        <ul class="nav nav-pills flex-column">
            @can('admin-only')
            <li class="nav-item mb-1">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            

            <li class="mt-3 mb-1 px-2 small text-uppercase text-muted fw-semibold">Administrativo</li>
            <li class="nav-item mb-1">
                <a href="{{ route('funcionarios.index') }}" class="nav-link {{ request()->routeIs('funcionarios.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-people me-2"></i> Funcionários
                </a>
            </li>

            <li class="mt-3 mb-1 px-2 small text-uppercase text-muted fw-semibold">Financeiro</li>
            <li class="nav-item mb-1">
                <a href="{{ route('pagamento.index') }}" class="nav-link {{ request()->routeIs('pagamento.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-cash-coin me-2"></i> Pagamentos
                </a>
            </li>

            <li class="mt-3 mb-1 px-2 small text-uppercase text-muted fw-semibold">Serviços</li>
            <li class="nav-item mb-1">
                <a href="{{ route('planos') }}" class="nav-link {{ request()->routeIs('planos*') ? 'active' : 'text-dark' }}">
                    <i class="bi-person-badge me-2"></i> Meu Plano
                </a>
            </li>
            @endcan
            <li class="nav-item mb-1">
                <a href="{{ route('dentista.index') }}" class="nav-link {{ request()->routeIs('dentista.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-person-badge me-2"></i> Dentistas
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('procedimento.index') }}" class="nav-link {{ request()->routeIs('procedimento.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-capsule me-2"></i> Procedimentos
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('paciente.index') }}" class="nav-link {{ request()->routeIs('paciente.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-heart-pulse me-2"></i> Pacientes
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('agendamento.index') }}" class="nav-link {{ request()->routeIs('agendamento.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-calendar-event me-2"></i> Agendamentos
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('filaAtendimento.index') }}" class="nav-link {{ request()->routeIs('filaAtendimento.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-person-lines-fill me-2"></i> Fila de Atendimentos
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('atendimento.index') }}" class="nav-link {{ request()->routeIs('atendimento.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-clipboard2-check me-2"></i> Atendimentos
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('remarcacao.index') }}" class="nav-link {{ request()->routeIs('remarcacao.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-calendar-range me-2"></i> Remarcação
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('filaAgendamentoEspera.index') }}" class="nav-link {{ request()->routeIs('filaAgendamentoEspera.*') ? 'active' : 'text-dark' }}">
                    <i class="bi-hourglass-split me-2"></i> Fila de Espera
                </a>
            </li>
        </ul>
    </div>

   
</aside>

<div class="sidebar-overlay" id="menuOverlay"></div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebarMenu");
    const overlay = document.getElementById("menuOverlay");
    const btnToggle = document.getElementById("toggleSidebar");

    function abrirFecharMenu() {
        sidebar.classList.toggle("show");
        overlay.classList.toggle("active");
    }

    if (btnToggle) {
        btnToggle.addEventListener("click", e => {
            e.preventDefault();
            e.stopPropagation();
            abrirFecharMenu();
        });
    }

    overlay.addEventListener("click", abrirFecharMenu);

    document.querySelectorAll(".nav-link").forEach(item => {
        item.addEventListener("click", () => {
            if (window.innerWidth < 992) abrirFecharMenu();
        });
    });

    window.addEventListener("resize", () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove("show");
            overlay.classList.remove("active");
        }
    });
});
</script>