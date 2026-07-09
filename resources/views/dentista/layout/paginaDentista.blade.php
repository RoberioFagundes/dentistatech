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
        html, body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .app-topstrip {
            position: fixed;
            top: 0; left: 0; right: 0; height: 50px;
            background-color: #00133c !important;
            z-index: 1030;
            display: flex; align-items: center; padding: 0 1.5rem;
        }
        .left-sidebar {
            width: 250px; position: fixed; top: 50px; bottom: 0; left: 0;
            background: #ffffff; border-right: 1px solid #dee2e6; z-index: 1010;
            overflow-y: auto; transition: transform 0.3s ease;
        }
        header.app-header {
            position: fixed; top: 50px; left: 250px; right: 0; height: 60px;
            background: #ffffff; z-index: 1000; border-bottom: 1px solid #dee2e6;
            display: flex; align-items: center; padding: 0 1rem; transition: all 0.3s ease;
        }
        .body-wrapper {
            margin-left: 250px; padding-top: 110px; min-height: 100vh; transition: all 0.3s ease;
        }
        .body-wrapper-inner { padding: 1.5rem 0; }
        .card {
            border-radius: 12px; border: 1px solid rgba(0, 0, 0, .08);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .01), 0 2px 4px -1px rgba(0, 0, 0, .01);
        }
        .table th, .table td { vertical-align: middle; white-space: normal !important; }

        @media (max-width: 991px) {
            .left-sidebar { top: 0; transform: translateX(-100%); z-index: 1040; }
            .left-sidebar.active { transform: translateX(0) !important; }
            header.app-header { left: 0; }
            .body-wrapper { margin-left: 0; padding-top: 110px; }
            .app-topstrip h3 { font-size: 0.95rem !important; }
        }
    </style>
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

        <div class="app-topstrip">
            <h3 class="text-white m-0 fs-5 w-100 text-center text-lg-start">Dentistatech</h3>
        </div>

        <aside class="left-sidebar" id="sidebar">
            <div class="d-flex flex-column h-100">
                <div class="brand-logo d-flex align-items-center justify-content-between p-3">
                    <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
                        <img src="{{ asset('favicon.ico') }}" height="250" alt="Logo" class="img-fluid">
                    </a>
                    <div class="close-btn d-lg-none cursor-pointer" id="sidebarCloseBtn">
                        <i class="fa fa-times fs-5"></i>
                    </div>
                </div>
                <nav class="sidebar-nav scroll-sidebar flex-grow-1 px-3">
                    @include('dentista.layout.menuDentista.menuDentista')
                </nav>
            </div>
        </aside>

        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand navbar-light w-100 d-flex justify-content-between align-items-center">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-lg-none">
                            <a class="nav-link sidebartoggler" id="sidebarToggleBtn" href="javascript:void(0)">
                                <i class="fa fa-bars fs-5"></i>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-user-circle fs-5"></i>
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                <div class="p-2">
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded text-danger">
                                            <i class="fa fa-sign-out me-2"></i> Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </nav>
            </header>

            <div class="body-wrapper-inner">
                <div class="container-fluid" style="margin-top: -150px;">
                    @yield('pagina')
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('template/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('template/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('template/assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const closeBtn = document.getElementById('sidebarCloseBtn');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                });
            }
            if (closeBtn && sidebar) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.remove('active');
                });
            }
            document.addEventListener('click', function(event) {
                if (sidebar && sidebar.classList.contains('active')) {
                    if (!sidebar.contains(event.target) && !event.target.closest('#sidebarToggleBtn')) {
                        sidebar.classList.remove('active');
                    }
                }
            });

            // AJAX dinâmico para os inputs com a classe .atualizar-campo
            $(document).on('change', '.atualizar-campo', function() {
                let id = $(this).data('id');
                let campo = $(this).data('campo');
                let valor = $(this).val();

                $.ajax({
                    url: '/agendamentos/' + id + '/atualizar-campo',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        campo: campo,
                        valor: valor
                    },
                    success: function(response) { console.log('Salvo com sucesso!'); },
                    error: function() { alert('Erro ao salvar as alterações do agendamento.'); }
                });
            });
        });
    </script>
      <div class="mt-4">
        @include('dentista.layout.footerDentista.footerDentista')
    </div>
</body>
</html>