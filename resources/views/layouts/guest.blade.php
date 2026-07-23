<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'dentistatech') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('logintemplate/images/icons/favicon.ico') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('logintemplate/css/main.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at top left, #eef6ff, #ffffff 45%),
                linear-gradient(135deg, #f8fbff, #edf5ff);
            overflow-x: hidden;
            position: relative;
        }

        /* Ajuste no container do card de registro */
        .card,
        .w-full.sm\:max-w-md {
            max-height: 75vh;
            /* Limita a altura a 85% da tela */
            overflow-y: auto;
            /* Ativa a barra de rolagem se passar do limite */
            scrollbar-width: thin;
        }

        /* Fundo com ondas */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("{{ asset('logintemplate/images/bg-wave.svg') }}") no-repeat bottom center;
            background-size: cover;
            opacity: .25;
            pointer-events: none;
            z-index: 0;
        }

        /* Pontos decorativos */
        body::after {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            right: 80px;
            bottom: 120px;
            background-image: radial-gradient(#56b9ff 2px, transparent 2px);
            background-size: 18px 18px;
            opacity: .35;
            z-index: 0;
        }

        .login-page {
            width: 100%;
            max-width: 1400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
            position: relative;
            z-index: 1;
        }

        /* Logo e Slogan */
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
            height: 40px;
            margin-top:-60px;
            width: 500px;
        }

        .slogan {
            font-size: 28px;
            letter-spacing: 8px;
            color: #15386d;
            font-weight: 500;
            margin-top: 15px;
            margin-bottom: 40px;
        }

        .slogan::before,
        .slogan::after {
            content: "";
            width: 120px;
            height: 4px;
            background: #24b4ff;
            display: inline-block;
            vertical-align: middle;
            margin: 0 25px;
        }


        /* Card Customizado que envolve o Formulário */
        .login-card {
            width: 100%;
            max-width: 620px;
            background: #fff;
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 30px 60px rgba(12, 71, 140, .12);
            margin-top: 60px;

            /* Adicionado para permitir rolagem interna caso o conteúdo seja extenso */
            max-height: 75vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        /* Estilização da barra de rolagem para navegadores baseados em Webkit (Chrome, Edge, Safari) */
        .login-card::-webkit-scrollbar {
            width: 6px;
        }

        .login-card::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        @media(max-width:768px) {
            .slogan {
                font-size: 16px;
                letter-spacing: 2px;
            }

            .slogan::before,
            .slogan::after {
                width: 40px;
            }

            .login-card {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="login-page">

        <header class="logo-container">
            <a href="{{ url('/') }}" aria-label="Ir para a página inicial">
                <x-application-logo class="w-32 h-32 mx-auto fill-current text-gray-500 hover:text-gray-700 transition-colors duration-200" />
            </a>
            <div class="slogan">DENTISTATECH</div>
        </header>

        <main class="login-card">
            {{ $slot }}
        </main>

        <footer class="mt-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
        </footer>
    </div>

    <script src="{{ asset('logintemplate/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('logintemplate/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('logintemplate/js/main.js') }}"></script>

</body>

</html>