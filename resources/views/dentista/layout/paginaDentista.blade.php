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
            font-family: system-ui, -apple-system, sans-serif;
        }

        .app-topstrip {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #00133c !important;
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
        }

        .left-sidebar {
            width: 250px;
            position: fixed;
            top: 50px;
            bottom: 0;
            left: 0;
            background: #ffffff;
            border-right: 1px solid #dee2e6;
            z-index: 1010;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        header.app-header {
            position: fixed;
            top: 50px;
            left: 250px;
            right: 0;
            height: 60px;
            background: #ffffff;
            z-index: 1000;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            transition: all 0.3s ease;
        }

        .body-wrapper {
            margin-left: 250px;
            padding-top: 110px;
            /* Reduza este valor */
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .body-wrapper-inner {
            padding: 1.5rem 0;
        }

        .card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, .08);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .01), 0 2px 4px -1px rgba(0, 0, 0, .01);
        }

        .table th,
        .table td {
            vertical-align: middle;
            white-space: normal !important;
        }
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
           
        }

        @media (max-width: 991px) {
            .left-sidebar {
                top: 0;
                transform: translateX(-100%);
                z-index: 1040;
            }

            .left-sidebar.active {
                transform: translateX(0) !important;
            }

            header.app-header {
                left: 0;
            }

            .body-wrapper {
                margin-left: 0;
                padding-top: 110px;
            }

            .app-topstrip h3 {
                font-size: 0.95rem !important;
            }
        }
    </style>
</head>

<body>
    <div class="page-wrapper mt-0" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <div class="app-topstrip">
            <h3 class="text-white m-0 fs-5 w-100 text-center text-lg-start">Dentistatech</h3>
        </div>

        <aside class="left-sidebar" id="sidebar">
            <div class="d-flex flex-column h-100">
                <div class="brand-logo p-3">
                    <a href="{{ route('dashboard') }}"><img src="{{ asset('favicon.ico') }}" height="150"
                            alt="Logo"></a>
                </div>

                <nav class="sidebar-nav scroll-sidebar flex-grow-1 px-3">
                    @include('dentista.layout.menuDentista.menuDentista')
                </nav>
            </div>
        </aside>

        <div class="body-wrapper">
            <header class="app-header">...</header>

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    @yield('pagina')
                </div>
            </div>

            @include('dentista.layout.footerDentista.footerDentista')
        </div>
        <!-- é isso que faz o modal funcionar -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="{{ asset('template/assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('template/assets/js/sidebarmenu.js') }}"></script>
<script src="{{ asset('template/assets/js/app.min.js') }}"></script>
</body>

</html>