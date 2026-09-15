<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dentistatech - Gestão Inteligente para Clínicas e Consultórios Odontológicos</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .hero-section {
            background: linear-gradient(135deg, #00133c 0%, #0d2154 100%);
            color: white;
            padding: 100px 0 80px 0;
        }

        .feature-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .btn-trial {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #00133c;
            border: none;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            transition: all 0.3s ease;
        }

        .btn-trial:hover {
            background: linear-gradient(135deg, #ffca2c 0%, #f57c00 100%);
            color: #00133c;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }

        footer {
            background: #000f29;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <!-- Cabeçalho / Navbar Pública -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #00133c;">
        <div class="container">
            <a class="navbar-brand fs-4" href="#"><i class="bi bi-hospital me-2"></i>Dentistatech</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#funcionalidades">Funcionalidades</a>
                    </li>
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary px-4">Painel / Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="text-white text-decoration-none me-3">Entrar</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-trial px-4 rounded-pill">Testar por 15 Dias</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Seção Principal (Hero) -->
    <header class="hero-section text-center text-lg-start" style="margin-top: 56px;">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-5 mb-lg-0">
                    <span class="badge bg-primary bg-opacity-25 text-white mb-3 px-3 py-2 rounded-pill">Gestão Odontológica Moderna</span>
                    <h1 class="display-4 fw-bold mb-3">Revolucione a gestão do seu consultório com o Dentistatech</h1>
                    <p class="lead text-white-50 mb-4">Centralize orçamentos, controle atendimentos diários, gerencie recebimentos e organize os prontuários dos seus pacientes em uma plataforma rápida, segura e totalmente integrada.</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-trial btn-lg px-4 rounded-pill"><i class="bi bi-rocket-takeoff me-2"></i>Testar por 15 Dias Grátis</a>
                        @endif
                        <a href="#funcionalidades" class="btn btn-outline-light btn-lg px-4 rounded-pill">Conhecer Recursos</a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-10 shadow-lg">
                        <i class="bi bi-shield-check display-1 text-info mb-3 d-block"></i>
                        <h3 class="h4 text-white">Controle Total na Palma da Mão</h3>
                        <p class="text-white-50 small mb-0">Interface otimizada para dentistas e recepcionistas ganharem agilidade no dia a dia clínico.</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Seção de Funcionalidades -->
    <section id="funcionalidades" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Tudo o que sua clínica precisa</h2>
                <p class="text-muted">Ferramentas desenhadas sob medida para otimizar os fluxos do seu consultório.</p>
            </div>

            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4 h-100 shadow-sm">
                        <div class="feature-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Atendimentos do Dia</h4>
                        <p class="text-muted small mb-0">Acompanhe em tempo real a agenda e o fluxo de atendimentos diários programados para o consultório.</p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4 h-100 shadow-sm">
                        <div class="feature-icon">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Orçamentos de Pacientes</h4>
                        <p class="text-muted small mb-0">Elabore orçamentos detalhados vinculados diretamente aos dados dos pacientes, com suporte a cálculos automáticos e descontos.</p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4 h-100 shadow-sm">
                        <div class="feature-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Gestão de Pagamentos</h4>
                        <p class="text-muted small mb-0">Registre recebimentos associados a diferentes formas de pagamento ativas, gerando confirmações de forma ágil e segura.</p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4 h-100 shadow-sm">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Controle de Pacientes</h4>
                        <p class="text-muted small mb-0">Mantenha cadastros completos com histórico de contatos, CPF e informações essenciais para o atendimento odontológico.</p>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4 h-100 shadow-sm">
                        <div class="feature-icon">
                            <i class="bi bi-list-nested"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Menu Modular Personalizado</h4>
                        <p class="text-muted small mb-0">Navegação estruturada por perfis e módulos específicos para dentistas, facilitando o acesso rápido às rotinas.</p>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4 h-100 shadow-sm">
                        <div class="feature-icon">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Totalmente Responsivo</h4>
                        <p class="text-muted small mb-0">Acesse de computadores, tablets ou smartphones com um layout adaptável e barra lateral otimizada para mobile.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer class="py-4 text-center">
        <div class="container">
            <p class="mb-1">&copy; {{ date('Y') }} Dentistatech. Todos os direitos reservados.</p>
            <small class="text-muted">Plataforma de Gestão Odontológica Integrada</small>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>