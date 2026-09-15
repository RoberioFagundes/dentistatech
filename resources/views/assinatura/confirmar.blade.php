@extends('dentista.layout.paginaDentista')
@section('pagina')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow border-0 rounded-lg p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="fs-1 text-success">✅</span>
                    <h3 class="fw-bold mt-2">Assinatura Gerada!</h3>
                    <p class="text-muted">Valor mensal: <strong>R$ {{ $valor }}</strong></p>
                </div>

                {{-- PIX --}}
                @if($tipo === 'pix')
                <div class="text-center p-4 border rounded bg-light mb-4">
                    <h5 class="fw-bold mb-3">📱 Pague via PIX</h5>
                    {{-- Ajuste: propriedade correta = $cobranca->pix->encodedImage --}}
                    <img src="data:image/png;base64,{{ $cobranca->pix->encodedImage }}" alt="QR Code PIX" class="img-fluid mb-3" style="max-width:250px">
                    <div class="alert alert-secondary small user-select-all mt-2">
                        {{ $cobranca->pix->payload }}
                    </div>
                    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="navigator.clipboard.writeText('{{ $cobranca->pix->payload }}').then(() => alert('Código copiado!'))">
                        Copiar Código PIX
                    </button>
                    <p class="small text-muted mt-2">Escaneie o QR Code ou copie o código acima no app do seu banco.</p>
                </div>
                @endif

                {{-- BOLETO --}}
                @if($tipo === 'boleto')
                <div class="text-center p-4 border rounded bg-light mb-4">
                    <h5 class="fw-bold mb-3">🧾 Boleto Gerado</h5>
                    <p class="mb-3">Vencimento: {{ \Carbon\Carbon::parse($cobranca->dueDate)->format('d/m/Y') }}</p>
                    <a href="{{ $cobranca->bankSlipUrl }}" target="_blank" class="btn btn-primary">Baixar Boleto em PDF</a>
                </div>
                @endif

                {{-- CARTÃO DE CRÉDITO --}}
                @if($tipo === 'credit_card')
                <div class="text-center p-4 border rounded bg-light mb-4">
                    <h5 class="fw-bold mb-3">💳 Pagamento Processado Imediatamente</h5>
                    <p class="mb-0">Status: 
                        <strong class="text-{{ $cobranca->status === 'CONFIRMED' ? 'success' : ($cobranca->status === 'PENDING' ? 'warning' : 'danger') }}">
                            {{ $cobranca->status }}
                        </strong>
                    </p>
                    @if($cobranca->status === 'CONFIRMED')
                        <p class="small text-success mt-2">Pagamento aprovado com sucesso!</p>
                    @endif
                </div>
                @endif

                <div class="text-center mt-4">
                    <a href="{{ route('dentista.dashboard') }}" class="btn btn-outline-primary">Ir para o Painel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection