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
        html,
        body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            background-color: #f8f9fa;
            font-family: system-ui, sans-serif;
        }

        .app-topstrip {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #00133c !important;
            z-index: 1060;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
        }

        /* Ajuste a sidebar para não empilhar errado */
        .left-sidebar {
            width: 250px;
            position: fixed;
            top: 50px;
            left: 0;
            bottom: 0;
            background: #fff;
            border-right: 1px solid #dee2e6;
            z-index: 1050;
            display: flex;
            flex-direction: column;
        }

        .brand-logo {
            flex-shrink: 0;
            /* Impede que o logo encolha */
            height: 100px;
            /* Ajuste conforme necessário */
        }

        /* Garante que a área de scroll pegue todo o espaço disponível abaixo da logo */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            /* Para navegadores modernos */
            padding-bottom: 50px;
            /* Adiciona respiro no final para não cortar o último item */
        }

        /* Garante que a UL não tenha margens que empurrem o conteúdo para baixo da tela */
        .patch-sidebar {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            padding-top: 10px !important;
        }

        .body-wrapper {
            margin-left: 250px;
            padding-top: 50px;
            transition: .3s;
        }

        @media (max-width: 991px) {
            .left-sidebar {
                transform: translateX(-100%);
            }

            .left-sidebar.active {
                transform: translateX(0) !important;
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
            }

            .body-wrapper {
                margin-left: 0 !important;
            }
        }

        /* Classes do seu Menu (Patch) */
        .patch-sidebar {
            display: block;
            max-height: calc(100vh - 90px);
            overflow-y: auto;
            padding: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 12px;
            text-decoration: none;
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, #0d6efd, #4b8dff) !important;
            color: #fff !important;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper">

        <div class="app-topstrip">
            <button id="toggleSidebar" class="btn btn-primary d-lg-none me-3">
                <i class="bi bi-list"></i>
            </button>
            <h3 class="text-white m-0 fs-5">Dentistatech</h3>
        </div>

        <aside class="left-sidebar" id="sidebar">
            <div class="p-3">
                <a href="{{ route('dashboard') }}"><img src="{{ asset('favicon.ico') }}" height="100" alt="Logo"></a>
            </div>
            <nav class="sidebar-nav px-3">
                @include('dentista.layout.menuDentista.menuDentista')
            </nav>
        </aside>

        <div class="body-wrapper">
            <div class="container-fluid py-4">
                @yield('pagina')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const toggle = document.getElementById("toggleSidebar");
            const wrapper = document.getElementById("main-wrapper");

            // Toggle do botão
            toggle.addEventListener("click", function(e) {
                e.stopPropagation();
                sidebar.classList.toggle("active");
                // Remove qualquer interferência de classe do template
                wrapper.classList.remove("mini-sidebar");
            });

            // Fechar ao clicar fora
            document.addEventListener("click", function(e) {
                if (window.innerWidth <= 991) {
                    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                        sidebar.classList.remove("active");
                    }
                }
            });
        });
    </script>
</body>

</html>