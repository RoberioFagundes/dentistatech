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
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        /* Responsividade para Telas Menores */
        @media (max-width: 991px) {
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
        <header class="app-topstrip">
            <!-- Lado Esquerdo: Botão Mobile e Identificação do Usuário -->
            <div class="d-flex align-items-center gap-3">
                <button id="toggleSidebar" class="d-lg-none" aria-label="Abrir Menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <span class="text-white">Usuário:</span>
                    <strong class="text-white text-truncate d-inline-block align-middle" style="max-width: 170px;">{{ auth()->user()->name }}</strong>
                </div>
            </div>

            <!-- Lado Direito: Perfil e Sair -->
            <div class="d-flex align-items-center gap-3">
                <a class="text-white text-decoration-none" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person me-1"></i> <span class="d-none d-sm-inline">Meu Perfil</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger text-decoration-none p-0">
                        <i class="bi bi-box-arrow-right me-1"></i> <span class="d-none d-sm-inline">Sair</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Sidebar - Apenas inclua o arquivo, todo estilo e lógica está nele -->
        @include('dentista.layout.menuDentista.menuDentista')

        <!-- Conteúdo das Páginas -->
        <div class="body-wrapper">
            <main class="container-fluid">
                @yield('pagina')
            </main>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('template/assets/libs/jquery/dist/jquery.min.js') }}"></script>
</body>
</html>