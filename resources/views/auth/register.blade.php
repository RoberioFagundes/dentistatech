<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nome do Usuário -->
        <div>
            <x-input-label for="name" :value="__('Nome do Responsável')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Dados da Clínica -->
        <div class="mt-4">
            <x-input-label for="razao_social" :value="__('Razão Social da Clínica')" />
            <x-text-input id="razao_social" class="block mt-1 w-full" type="text" name="razao_social" :value="old('razao_social')" required />
            <x-input-error :messages="$errors->get('razao_social')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="cnpj" :value="__('CNPJ')" />
            <x-text-input id="cnpj" class="block mt-1 w-full" type="text" name="cnpj" :value="old('cnpj')" required />
            <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="telefone" :value="__('Telefone')" />
            <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone')" required />
            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="endereco" :value="__('Endereço (Rua, Avenida, etc)')" />
            <x-text-input id="endereco" class="block mt-1 w-full" type="text" name="endereco" :value="old('endereco')" required />
            <x-input-error :messages="$errors->get('endereco')" class="mt-2" />
        </div>

        <!-- CEP e Número (Novos campos obrigatórios para o Asaas) -->
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="cep" :value="__('CEP (somente números)')" />
                <x-text-input id="cep" class="block mt-1 w-full" type="text" name="cep" maxlength="8" :value="old('cep')" required placeholder="Ex: 01001000" />
                <x-input-error :messages="$errors->get('cep')" class="mt-2" />
            </div>
             <div>
                <x-input-label for="bairro" :value="__('bairro')" />
                <x-text-input id="bairro" class="block mt-1 w-full" type="text" name="bairro" :value="old('bairro')" required />
                <x-input-error :messages="$errors->get('bairro')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="complemento" :value="__('complemento')" />
                <x-text-input id="complemento" class="block mt-1 w-full" type="text" name="complemento" :value="old('complemento')" required />
                <x-input-error :messages="$errors->get('complemento')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="numero" :value="__('Número')" />
                <x-text-input id="numero" class="block mt-1 w-full" type="text" name="numero" :value="old('numero')" required placeholder="Ex: 123 ou S/N" />
                <x-input-error :messages="$errors->get('numero')" class="mt-2" />
            </div>
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Senha -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Já registrado?') }}
            </a>

            <button class="btn btn-primary ms-3">
                 {{ __('Cadastrar') }}
            </button>
        </div>
    </form>
</x-guest-layout>