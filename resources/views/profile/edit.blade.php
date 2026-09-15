@extends('dentista.layout.paginaDentista')

@section('pagina')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            {{-- Atualizar Dados do Perfil --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-secondary mb-4">Informações do Perfil</h5>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Alterar Senha --}}
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-secondary mb-4">Alterar Senha</h5>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Excluir Conta --}}
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-danger mb-3">Excluir Conta</h5>
                    <p class="text-muted small mb-3">Ao excluir sua conta, todos os seus dados serão removidos permanentemente.</p>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</div>
@endsection