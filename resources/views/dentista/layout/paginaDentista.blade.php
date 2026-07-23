<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dentistatech</title>

    <link rel="stylesheet" href="{{ asset('template/assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Barra Superior */
        .app-topstrip {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: linear-gradient(135deg, #00133c 0%, #0d2154 100%);
            z-index: 1060;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .app-topstrip h3 {
            color: #ffffff;
            font-weight: 600;
            font-size: 1.2rem;
            margin: 0;
            letter-spacing: 0.5px;
        }

        /* Botão de Menu Mobile */
        #toggleSidebar {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 0.4rem 0.6rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #toggleSidebar:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Sidebar */
        .left-sidebar {
            width: 270px;
            position: fixed;
            top: 64px;
            left: 0;
            bottom: 0;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.02);
        }

        .brand-logo {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }

        .brand-logo img {
            height: 60px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .brand-logo img:hover {
            transform: scale(1.03);
        }

        /* Navegação */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0.85rem;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        /* Links do Menu */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            margin: 4px 0;
            border-radius: 10px;
            text-decoration: none;
            color: #475569;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover {
            background: #f1f5f9;
            color: #0f172a;
            padding-left: 18px;
        }

        .sidebar-link:hover i {
            color: #0d6efd;
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #0d6efd 0%, #2563eb 100%);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        }

        .sidebar-link.active i {
            color: #ffffff !important;
        }

        /* Área Inferior Fixada (Perfil e Sair) */
        .sidebar-footer {
            padding: 1rem 0.85rem;
            border-top: 1px solid #f1f5f9;
            background: #f8fafc;
        }

        .btn-logout {
            width: 100%;
            text-align: left;
            border: none;
            background: transparent;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-logout:hover {
            background: #fef2f2 !important;
            color: #dc2626 !important;
            padding-left: 18px;
        }

        .btn-logout:hover i {
            color: #dc2626 !important;
        }

        /* Conteúdo Principal */
        .body-wrapper {
            margin-left: 270px;
            padding-top: 64px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .container-fluid {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Overlay para mobile quando o menu abrir */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1045;
            backdrop-filter: blur(2px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Responsividade para Telas Menores */
        @media (max-width: 991px) {
            .left-sidebar {
                transform: translateX(-100%);
            }

            .left-sidebar.active {
                transform: translateX(0);
            }

            .body-wrapper {
                margin-left: 0 !important;
            }

            .container-fluid {
                padding: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper">

        <!-- Barra Superior -->
        <header class="app-topstrip d-flex align-items-center justify-content-between px-4 py-3 bg-white shadow-sm border-bottom">

            <!-- Lado Esquerdo: Menu Mobile e Logo/Título -->
            <div class="d-flex align-items-center">
                <button id="toggleSidebar" class="d-lg-none me-3 btn btn-light border-0 p-2" aria-label="Abrir Menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h3 class="m-0 fw-bold text-primary fs-4">Dentistatech</h3>
            </div>

            <!-- Lado Direito: Ações do Usuário (Perfil e Sair) -->
            <div class="d-flex align-items-center gap-3">
                <!-- Editar Perfil -->
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-pill text-decoration-none">
                    <i class="bi bi-person-gear fs-5"></i>
                    <span class="d-none d-sm-inline">Meu Perfil</span>
                </a>

                <!-- Botão Sair -->
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-pill">
                        <i class="bi bi-box-arrow-right fs-6"></i>
                        <span class="d-none d-sm-inline">Sair</span>
                    </button>
                </form>
            </div>

        </header>

        <aside class="left-sidebar" id="sidebar">
            <!-- Logo no Topo -->
            <div class="brand-logo">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('favicon.ico') }}" alt="Logo Dentistatech">
                </a>
            </div>



            <!-- Itens do Menu Principal (Meio com Scroll) -->
            <nav class="sidebar-nav">
                @include('dentista.layout.menuDentista.menuDentista')
            </nav>

            <!-- Área Inferior: Perfil e Sair (Fixo na Base, um embaixo do outro) -->

        </aside>

        <!-- Overlay de Fundo para Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Conteúdo das Páginas -->
        <div class="body-wrapper">
            <main class="container-fluid">
                @yield('pagina')
            </main>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const toggle = document.getElementById("toggleSidebar");
            const overlay = document.getElementById("sidebarOverlay");

            function toggleMenu() {
                sidebar.classList.toggle("active");
                overlay.classList.toggle("active");
            }

            // Abre/fecha menu no mobile
            toggle.addEventListener("click", function(e) {
                e.stopPropagation();
                toggleMenu();
            });

            // Fecha menu ao clicar no overlay escuro
            overlay.addEventListener("click", function() {
                toggleMenu();
            });

            // Fecha menu ao redimensionar para desktop
            window.addEventListener("resize", function() {
                if (window.innerWidth > 991) {
                    sidebar.classList.remove("active");
                    overlay.classList.remove("active");
                }
            });
        });
    </script>
</body>

</html>