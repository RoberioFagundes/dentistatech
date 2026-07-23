<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentistaTech - Sistema para Clínicas Odontológicas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonte Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Cabeçalho -->
    <header class="bg-white/95 backdrop-blur-md shadow-sm sticky top-0 z-50 transition-all">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="flex items-center gap-2.5 group">
                <span class="text-3xl transition-transform group-hover:scale-110">🦷</span>
                <h1 class="text-2xl font-extrabold text-blue-700 tracking-tight">DentistaTech</h1>
            </a>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-blue-700 font-semibold hover:text-blue-900 transition">
                        Painel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="font-semibold text-gray-600 hover:text-blue-700 transition">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-blue-800 transition shadow-md shadow-blue-700/20 active:scale-95">
                        Cadastrar
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Banner Principal em Formato de Card / Container Centralizado -->
  

    <!-- Funcionalidades -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-blue-700 font-bold uppercase tracking-wider text-sm">O que oferecemos</span>
                <h2 class="text-3xl md:text-4xl font-extrabold mt-2 mb-4 text-gray-900 tracking-tight">
                    Tudo para sua clínica funcionar melhor
                </h2>
                <p class="text-gray-600 text-lg">
                    Recursos modernos e intuitivos pensados especialmente para profissionais da odontologia.
                </p>
            </div>

            <div class="card text-center">
                <!-- Card 1 -->
             
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">👨‍⚕️</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Dentistas e Equipe</h3>
                    <p class="text-gray-600 leading-relaxed">Cadastro completo, controle individual de horários e permissões por profissional.</p>
                

                <!-- Card 2 -->
                
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">🧑</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Pacientes</h3>
                    <p class="text-gray-600 leading-relaxed">Ficha completa, histórico detalhado de atendimentos e dados de contato organizados.</p>
                

                <!-- Card 3 -->
                
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">📅</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Agendamentos</h3>
                    <p class="text-gray-600 leading-relaxed">Agenda visual dinâmica, envio de lembretes e total organização por profissional.</p>
                

                <!-- Card 4 -->
                
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">⏳</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Fila de Espera</h3>
                    <p class="text-gray-600 leading-relaxed">Gerencie facilmente quem aguarda por encaixes ou vagas disponíveis de última hora.</p>
                

                <!-- Card 5 -->
                
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">🦷</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Odontograma Interativo</h3>
                    <p class="text-gray-600 leading-relaxed">Registre procedimentos, tratamentos e condições diretamente no odontograma digital.</p>
                

                <!-- Card 6 -->
                
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">📄</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Orçamentos</h3>
                    <p class="text-gray-600 leading-relaxed">Crie orçamentos detalhados e profissionais para enviar aos pacientes com rapidez.</p>
                

                <!-- Card 7 -->
                
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">💰</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Financeiro</h3>
                    <p class="text-gray-600 leading-relaxed">Controle completo de recebimentos, fluxo de caixa, contas a pagar e relatórios.</p>
                

                <!-- Card 8 -->
                
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">📝</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Procedimentos</h3>
                    <p class="text-gray-600 leading-relaxed">Cadastre seu catálogo de serviços e acompanhe o histórico do que foi realizado.</p>
                

                <!-- Card 9 -->
                
                    <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center text-3xl mb-5 group-hover:bg-blue-100 transition">📊</div>
                    <h3 class="font-bold text-xl mb-2 text-gray-900">Relatórios</h3>
                    <p class="text-gray-600 leading-relaxed">Tenha uma visão panorâmica e estratégica do faturamento e desempenho geral da clínica.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Plano -->
    <section id="planos" class="py-24 bg-gradient-to-b from-blue-50/60 to-white">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center max-w-xl mx-auto mb-16">
                <span class="text-blue-700 font-bold uppercase tracking-wider text-sm">Plano Único e Completo</span>
                <h2 class="text-3xl md:text-4xl font-extrabold mt-2 mb-4 text-gray-900 tracking-tight">
                    Tudo o que você precisa por um preço justo
                </h2>
            </div>

            <div class="max-w-lg mx-auto bg-white rounded-3xl shadow-2xl p-8 md:p-10 border-2 border-blue-100 relative overflow-hidden">
                <!-- Destaque visual no card -->
                

                <h3 class="text-xl font-bold text-gray-800 mb-2">Plano Profissional Completo</h3>
                <p class="text-sm text-gray-500 mb-6">Sem taxas de implantação ou contratos de fidelidade.</p>

                <div class="flex items-baseline justify-start gap-1 mb-6 pb-6 border-b border-gray-100">
                    <span class="text-5xl md:text-6xl font-extrabold text-blue-700">R$ 150</span>
                    <span class="text-gray-500 font-medium">/ mês</span>
                </div>

                <ul class="text-left space-y-4 mb-8">
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">✓</span> Cadastro ilimitado de pacientes
                    </li>
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">✓</span> Cadastro ilimitado de dentistas
                    </li>
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">✓</span> Agenda completa e lembretes inteligentes
                    </li>
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">✓</span> Odontograma interativo completo
                    </li>
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">✓</span> Controle financeiro e fluxo de caixa
                    </li>
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">✓</span> Emissão de orçamentos e relatórios
                    </li>
                    <li class="flex items-center gap-3 text-gray-700">
                        <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">✓</span> Suporte prioritário via e-mail e WhatsApp
                    </li>
                    <li class="flex items-center gap-3 font-semibold text-blue-700 pt-4 border-t border-gray-100">
                        <span class="text-lg">🎁</span> Primeiros 15 dias totalmente grátis
                    </li>
                </ul>

                <a href="{{ route('register') }}"
                   class="block w-full text-center bg-blue-700 text-white rounded-xl py-4 text-lg font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-700/25 active:scale-95">
                    Quero começar meu teste
                </a>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer class="bg-gray-900 text-gray-400 py-16 border-t border-gray-800">
        <div class="max-w-6xl mx-auto px-6 text-center md:text-left flex flex-col md:flex-row justify-between items-center gap-8">
            <div>
                <div class="flex items-center justify-center md:justify-start gap-2.5 mb-3">
                    <span class="text-3xl">🦷</span>
                    <h3 class="text-2xl font-bold text-white tracking-tight">DentistaTech</h3>
                </div>
                <p class="text-gray-400 max-w-sm">
                    O sistema de gestão definitivo desenvolvido para simplificar a rotina de clínicas e consultórios odontológicos.
                </p>
            </div>
            
            <div class="text-center md:text-right">
                <p class="text-sm text-gray-500 mb-2">
                    © {{ date('Y') }} DentistaTech. Todos os direitos reservados.
                </p>
                <p class="text-xs text-gray-600">
                    Feito com foco na excelência e inovação para profissionais da saúde.
                </p>
                <p>
                    contato@dentistatech.com.br
                </p>
            </div>
        </div>
    </footer>

</body>
</html>