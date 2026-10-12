🦷 DentistaTech — Plataforma de Gestão Odontológica
Sistema Web completo para gestão integrada de consultórios odontológicos.
Disponível em: https://dentistatech.com.br/
📋 Descrição
O DentistaTech é uma plataforma desenvolvida para centralizar e simplificar a rotina de clínicas e consultórios odontológicos. Reúne em um só lugar o controle de pacientes, agendamentos, orçamentos, pagamentos e prontuários, com uma interface moderna, rápida e totalmente responsiva.
✨ Principais Funcionalidades
Tabela
Módulo	Descrição
📅 Atendimentos do Dia	Acompanhamento em tempo real da agenda e fluxo de atendimentos programados
💰 Orçamentos	Elaboração de orçamentos detalhados vinculados ao cadastro do paciente, com cálculos automáticos e descontos
💳 Gestão de Pagamentos	Registro de recebimentos por diferentes formas de pagamento, com confirmações
📇 Controle de Pacientes	Cadastro completo com histórico, contatos, CPF e dados essenciais
🧭 Menu Modular	Navegação organizada por perfis (dentista, recepção) com acesso rápido às rotinas
📱 Totalmente Responsivo	Acesso via computador, tablet ou celular — interface adaptada para todos os tamanhos de tela
🛠️ Tecnologias Utilizadas
plaintext
Frontend:
  ├── HTML5           → Estrutura semântica e acessível
  ├── CSS3 / SCSS     → Estilização, variáveis e layout responsivo
  ├── JavaScript      → Interatividade, validações e dinâmicas de interface
  └── Bootstrap 5     → Componentes e grid adaptável

Backend:
  ├── PHP 8.x         → Lógica de negócio e comunicação com o banco
  └── Laravel 10.x    → Estrutura organizada, rotas, autenticação e segurança

Banco de Dados:
  ├── MySQL 8.x       → Armazenamento estruturado de pacientes, agendas, orçamentos
  └── Eloquent ORM    → Modelagem e relacionamentos entre entidades

Infraestrutura & Ferramentas:
  ├── Git             → Controle de versões
  ├── Docker (estudo) → Ambiente de desenvolvimento isolado
  └── Figma / Adobe XD → Prototipagem e design de interface
📐 Arquitetura do Sistema
plaintext
dentistatech/
├── public/              # Arquivos acessíveis publicamente
│   ├── css/             # Estilos compilados
│   ├── js/              # Scripts do frontend
│   ├── img/             # Imagens e ícones
│   └── index.php        # Ponto de entrada
├── app/                 # Lógica da aplicação
│   ├── Models/          # Entidades: Paciente, Atendimento, Orçamento...
│   ├── Http/
│   │   ├── Controllers/ # Controladores das ações
│   │   └── Middleware/  # Filtros de acesso e segurança
│   └── Views/           # Camada de apresentação
├── database/
│   ├── migrations/      # Estrutura e criação de tabelas
│   └── seeders/         # Dados padrão do sistema
├── resources/
│   └── views/           # Arquivos de interface
├── routes/              # Definição de endpoints
└── composer.json        # Dependências do projeto
Principais Entidades
Paciente → nome, CPF, contato, endereço, histórico
Atendimento → data, profissional, status, observações
Orçamento → itens, valores, descontos, situação
Pagamento → forma, valor recebido, data de confirmação
🚀 Instalação e Execução Local
bash
# 1. Clonar o repositório
git clone https://github.com/RoberioFagundes/dentistatech.git
cd dentistatech

# 2. Instalar dependências do PHP
composer install

# 3. Copiar arquivo de configuração
cp .env.example .env

# 4. Gerar chave da aplicação
php artisan key:generate

# 5. Configurar banco de dados no arquivo .env
DB_HOST=localhost
DB_DATABASE=dentistatech
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# 6. Executar criação das tabelas
php artisan migrate

# 7. Iniciar servidor de desenvolvimento
php artisan serve
Acessar em: http://localhost:8000
📅 Cronograma de Desenvolvimento
Tabela
Etapa	Período	Status
Modelagem do banco de dados	Jan/2025	✅ Concluído
Protótipo de interface	Fev/2025	✅ Concluído
Desenvolvimento do core (cadastros)	Mar/2025	✅ Concluído
Módulo de orçamentos	Abr/2025	✅ Concluído
Módulo de pagamentos	Mai/2025	✅ Concluído
Interface responsiva	Jun/2025	✅ Concluído
Disponibilização em produção	Jul/2025	✅ Concluído
Período de teste gratuito	Ago/2025 → atual	🟡 Em andamento
👤 Autor
Robério Fagundes dos Santos
Desenvolvedor Web Full-Stack
Palmas de Monte Alto – Bahia
🔗 Portfólio: https://dentistatech.com.br/
📧 Contato: roberiopma@hotmail.com
📄 Licença
Este projeto é de uso próprio e demonstração. Código-fonte disponível para fins de avaliação e portfólio.
🎯 Finalidade
Este repositório foi criado para:
✅ Demonstração de habilidades em desenvolvimento full-stack
✅ Comprovação de projeto publicado em operação
✅ Apoio à participação em processos seletivos e oportunidades profissionais
