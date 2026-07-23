<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos e Assinatura - DentistaTech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased">

    <!-- Cabeçalho -->
    <header class="bg-white/95 backdrop-blur-md shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="text-3xl">🦷</span>
                <h1 class="text-2xl font-extrabold text-blue-700">DentistaTech</h1>
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-blue-700 font-medium">Voltar</a>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-12">
        <!-- Alerta de teste -->
        @if($clinica->onTrial())
        <div class="mb-8 bg-blue-50 border border-blue-200 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⏳</span>
                <div>
                    <h3 class="font-semibold text-blue-800">Período de Teste Ativo</h3>
                    <p class="text-blue-600 text-sm">Seu teste acaba em {{ $clinica->trial_ends_at->diffForHumans() }}. Assine agora e não perca acesso!</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Card do Plano -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-700 to-cyan-600 text-white p-8 text-center">
                <h2 class="text-3xl font-extrabold">Plano Profissional Completo</h2>
                <p class="text-blue-100 mt-2">Tudo o que sua clínica precisa por um preço justo</p>
                <div class="mt-4">
                    <span class="text-5xl font-bold">R$ 150</span>
                    <span class="text-xl"> / mês</span>
                </div>
            </div>

            <div class="p-8">
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center gap-3"><span class="text-green-500 font-bold">✓</span> Cadastro ilimitado de pacientes</li>
                    <li class="flex items-center gap-3"><span class="text-green-500 font-bold">✓</span> Cadastro ilimitado de dentistas</li>
                    <li class="flex items-center gap-3"><span class="text-green-500 font-bold">✓</span> Agenda completa e lembretes</li>
                    <li class="flex items-center gap-3"><span class="text-green-500 font-bold">✓</span> Odontograma interativo</li>
                    <li class="flex items-center gap-3"><span class="text-green-500 font-bold">✓</span> Controle financeiro e relatórios</li>
                    <li class="flex items-center gap-3"><span class="text-green-500 font-bold">✓</span> Emissão de orçamentos</li>
                    <li class="flex items-center gap-3"><span class="text-green-500 font-bold">✓</span> Suporte prioritário</li>
                    <li class="flex items-center gap-3 font-semibold text-blue-700 pt-3 border-t">
                        <span>🎁</span> Seus primeiros 15 dias já estão grátis!
                    </li>
                </ul>

                <!-- Formulário de Pagamento -->
                <form action="{{ route('assinatura.criar') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block font-medium mb-2">Forma de Pagamento</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="forma_pagamento" value="CREDIT_CARD" class="peer hidden" checked>
                                <div class="border-2 border-gray-200 peer-checked:border-blue-600 rounded-xl p-3 text-center transition">
                                    💳<br><span class="text-sm">Cartão de Crédito</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="forma_pagamento" value="PIX" class="peer hidden">
                                <div class="border-2 border-gray-200 peer-checked:border-blue-600 rounded-xl p-3 text-center transition">
                                    📱<br><span class="text-sm">Pix</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="forma_pagamento" value="BOLETO" class="peer hidden">
                                <div class="border-2 border-gray-200 peer-checked:border-blue-600 rounded-xl p-3 text-center transition">
                                    🧾<br><span class="text-sm">Boleto</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-700 text-white py-4 rounded-xl text-lg font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-700/25">
                        Assinar e Continuar
                    </button>

                    <p class="text-center text-sm text-gray-500 mt-4">
                        Ao clicar, você concorda com a cobrança mensal via Asaas. Sem contratos de fidelidade.
                    </p>
                </form>
            </div>
        </div>
    </main>

</body>
</html>