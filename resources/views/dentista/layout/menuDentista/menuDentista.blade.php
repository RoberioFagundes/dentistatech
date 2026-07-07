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

/* Scroll */
.patch-sidebar{
    display:block;
    max-height:calc(100vh - 90px);
    overflow-y:auto;
    overflow-x:hidden;
    padding:8px;
    scrollbar-width:thin;
    scrollbar-color:#b9c6da transparent;
}

.patch-sidebar::-webkit-scrollbar{
    width:6px;
}

.patch-sidebar::-webkit-scrollbar-thumb{
    background:#c7d2e2;
    border-radius:20px;
}

.patch-sidebar::-webkit-scrollbar-thumb:hover{
    background:#9fb5d8;
}

/* Categorias */

.nav-small-cap{
    margin-top:18px;
    margin-bottom:8px;
    padding:0 14px;
    font-size:.72rem;
    color:#8b95a5!important;
    font-weight:700;
    letter-spacing:1.5px;
    text-transform:uppercase;
}

/* Links */

.sidebar-item{
    margin-bottom:4px;
}

.sidebar-link{
    display:flex;
    align-items:center;
    gap:14px;
    padding:12px 14px;
    border-radius:12px;
    color:var(--sidebar-text)!important;
    transition:.25s;
    position:relative;
}

.sidebar-link i{
    font-size:20px;
    width:24px;
    text-align:center;
    transition:.25s;
}

/* Hover */

.sidebar-link:hover{
    background:var(--sidebar-hover);
    color:var(--sidebar-active)!important;
    transform:translateX(4px);
}

.sidebar-link:hover i{
    transform:scale(1.15);
}

/* Item ativo */

.sidebar-link.active{
    background:linear-gradient(90deg,#0d6efd,#4b8dff)!important;
    color:#fff!important;
    box-shadow:0 8px 22px rgba(13,110,253,.22);
}

.sidebar-link.active i{
    color:#fff;
}

/* Barra lateral do item ativo */

.sidebar-link.active::before{
    content:"";
    position:absolute;
    left:-8px;
    top:8px;
    bottom:8px;
    width:5px;
    border-radius:10px;
    background:#0d6efd;
}

/* Texto */

.hide-menu{
    font-weight:500;
    font-size:.94rem;
}

/* Cards de categoria */

.nav-small-cap:first-child{
    margin-top:6px;
}

/* Separação */

.sidebar-item:last-child{
    margin-bottom:15px;
}

/* Animação */

.sidebar-link{
    animation:fadeMenu .25s ease;
}

@keyframes fadeMenu{

    from{
        opacity:0;
        transform:translateX(-8px);
    }

    to{
        opacity:1;
        transform:translateX(0);
    }

}
</style>

<ul id="sidebarnav" class="nav flex-column patch-sidebar">
    <!-- HOME -->
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

    <!-- FINANCEIRO -->
    <li class="nav-small-cap mt-3 mb-1 text-uppercase text-muted fs-7 fw-bold" style="letter-spacing: 1px;">
        <span class="hide-menu">Financeiro</span>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('financeiro.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ Route::has('financeiro.index') ? route('financeiro.index') : '#' }}">
            <i class="bi bi-person-badge fs-5"></i> 
            <span class="hide-menu fw-medium">Pagamentos</span>
        </a>
    </li>

    <!-- SERVIÇOS -->
    <li class="nav-small-cap mt-3 mb-1 text-uppercase text-muted fs-7 fw-bold" style="letter-spacing: 1px;">
        <span class="hide-menu">Serviços</span>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('dentista.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ Route::has('dentista.index') ? route('dentista.index') : '#' }}">
            <i class="bi bi-person-badge fs-5"></i> 
            <span class="hide-menu fw-medium">Dentistas</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('procedimento.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ route('procedimento.index') }}">
            <i class="bi bi-capsule fs-5"></i> 
            <span class="hide-menu fw-medium">Procedimentos</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('paciente.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ route('paciente.index') }}">
            <i class="bi bi-heart-pulse fs-5"></i> 
            <span class="hide-menu fw-medium">Pacientes</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('agendamento.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ route('agendamento.index') }}">
            <i class="bi bi-calendar-event fs-5"></i>
            <span class="hide-menu fw-medium">Agendamentos</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('filaAtendimento.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ route('filaAtendimento.index') }}">
            <i class="bi bi-person-lines-fill fs-5"></i>
            <span class="hide-menu fw-medium">Fila de Atendimentos</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('atendimento.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ route('atendimento.index') }}">
            <i class="bi bi-clipboard2-check fs-5"></i>
            <span class="hide-menu fw-medium">Atendimentos</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('filaAgendamentoEspera.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ route('filaAgendamentoEspera.create') }}">
            <i class="bi bi-hourglass-split fs-5"></i>
            <span class="hide-menu fw-medium">Fila de Espera</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link d-flex align-items-center gap-3 py-2 px-3 rounded-2 text-decoration-none {{ request()->routeIs('remarcacao.*') ? 'active bg-primary text-white' : 'text-dark' }}"
           href="{{ route('remarcacao.index') }}">
            <i class="bi bi-calendar-range fs-5"></i>
            <span class="hide-menu fw-medium">Remarcação</span>
        </a>
    </li>
</ul>