<ul id="sidebarnav" class="nav flex-column patch-sidebar">
    <li class="nav-small-cap mt-2 mb-1 text-uppercase text-muted fs-7 fw-bold" style="letter-spacing: 1px;">
        <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4 align-middle"></iconify-icon>
        <span class="hide-menu ms-1">Home</span>
    </li>
    
    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('dashboard') ? 'active bg-primary text-white' : 'text-dark' }}" 
           href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2 fs-5"></i>
            <span class="hide-menu fw-medium">Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('profile.*') ? 'active bg-primary text-white' : 'text-dark' }}" 
           href="{{ route('profile.edit') }}">
            <i class="bi bi-person-gear fs-5"></i>
            <span class="hide-menu fw-medium">Meu Perfil</span>
        </a>
    </li>

    <li class="nav-small-cap mt-3 mb-1 text-uppercase text-muted fs-7 fw-bold" style="letter-spacing: 1px;">
        <span class="hide-menu">Serviços</span>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('pessoas.*') ? 'active bg-primary text-white' : 'text-dark' }}" href="#">
            <i class="bi bi-people fs-5"></i>
            <span class="fw-medium">Paciente</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('agendamentos.*') ? 'active bg-primary text-white' : 'text-dark' }}" href="#">
            <i class="bi bi-calendar-event fs-5"></i>
            <span class="fw-medium">Agendamentos</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('filaAgendamentoEspera.*') ? 'active bg-primary text-white' : 'text-dark' }}" href="#">
            <i class="bi bi-hourglass-split fs-5"></i>
            <span class="hide-menu fw-medium">Fila de Espera</span>
        </a>
    </li>
    
    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('marcacao.*') ? 'active bg-primary text-white' : 'text-dark' }}" href="#">
            <i class="bi bi-calendar-range fs-5"></i>
            <span class="fw-medium">Remarcação</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('filas.*') ? 'active bg-primary text-white' : 'text-dark' }}" href="#">
            <i class="bi bi-person-lines-fill fs-5"></i>
            <span class="hide-menu fw-medium">Fila de Atendimentos</span>
        </a>
    </li>
   
    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('atendimentos.*') ? 'active bg-primary text-white' : 'text-dark' }}" href="#">
            <i class="bi bi-clipboard2-check fs-5"></i>
            <span class="hide-menu fw-medium">Atendimentos</span>
        </a>
    </li>
</ul>

<style>
.sidebar-item a.sidebar-link:not(.active):hover {
    background-color: rgba(0, 0, 0, 0.05);
    color: var(--bs-primary) !important;
    transition: all 0.2s ease-in-out;
}
</style>