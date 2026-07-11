@extends('dentista.layout.paginaDentista')

@section('pagina')
    @php
        $isEdit = isset($agendamento);
        $route = $isEdit ? route('agendamento.update', $agendamento->id) : route('agendamento.store');
    @endphp

    <div class="container my-5">
        <div class="card shadow-sm border-0 max-w-4xl mx-auto">

            <div class="card-header bg-white border-bottom py-3">
                <h2 class="card-title h4 mb-1 d-flex align-items-center gap-2 font-weight-bold">
                    @if($isEdit)
                        <span class="text-warning">✏️</span> Editar Agendamento
                    @else
                        <span class="text-primary">📅</span> Novo Agendamento
                    @endif
                </h2>
                <p class="text-muted small mb-0">Preencha os dados abaixo para organizar o horário no consultório.</p>
            </div>

            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="font-weight-bold mb-1">⚠️ Ops! Verifique os erros abaixo:</div>
                        <ul class="mb-0 px-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ $route }}" method="POST">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <div class="row g-3">

                        <div class="col-12 col-md-4">
                            <label for="data"
                                class="form-label text-secondary small text-uppercase font-weight-bold">Data</label>
                            <input type="date" id="data" name="data" required
                                value="{{ old('data', $isEdit ? $agendamento->data->format('Y-m-d') : '') }}"
                                class="form-control py-2 @error('data') is-invalid @enderror">
                            @error('data')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="hora_inicio"
                                class="form-label text-secondary small text-uppercase font-weight-bold">Hora Início</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" required
                                value="{{ old('hora_inicio', $isEdit ? $agendamento->hora_inicio : '') }}"
                                class="form-control py-2 @error('hora_inicio') is-invalid @enderror">
                            @error('hora_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="hora_fim"
                                class="form-label text-secondary small text-uppercase font-weight-bold">Hora Fim</label>
                            <input type="time" id="hora_fim" name="hora_fim" required
                                value="{{ old('hora_fim', $isEdit ? $agendamento->hora_fim : '') }}"
                                class="form-control py-2 @error('hora_fim') is-invalid @enderror">
                            @error('hora_fim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @php
                            $pacienteSelecionadoId = old('paciente_id', $isEdit ? $agendamento->paciente_id : request('paciente_id'));
                            $pacienteFixo = !$isEdit && request()->has('paciente_id');
                        @endphp
                        <div class="col-12 col-md-8">
                            <label for="paciente_id"
                                class="form-label text-secondary small text-uppercase font-weight-bold">Paciente</label>

                            <select id="paciente_id" name="paciente_id"
                                class="form-select py-2 @error('paciente_id') is-invalid @enderror" {{ $pacienteFixo ? 'disabled' : 'required' }}>

                                <option value="">Selecione o Paciente</option>

                                @foreach($pacientes as $p)
                                    {{-- Verifica se o ID do paciente no loop é o selecionado --}}
                                    <option value="{{ $p->id }}" {{ ($pacienteSelecionadoId == $p->id) ? 'selected' : '' }}>
                                        👤 {{ $p->nome }} {{ $p->sobrenome }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Se for fixo, o hidden garante que o ID seja enviado no request mesmo com o select
                            desabilitado --}}
                            @if($pacienteFixo)
                                <input type="hidden" name="paciente_id" value="{{ $pacienteSelecionadoId }}">
                            @endif

                            @error('paciente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="status"
                                class="form-label text-secondary small text-uppercase font-weight-bold">Status</label>
                            <select id="status" name="status" required
                                class="form-select py-2 @error('status') is-invalid @enderror">
                                @php
                                    $statusAtual = old('status', $isEdit ? $agendamento->status : 'AGENDADO');
                                @endphp
                                <option value="AGENDADO" {{ $statusAtual == 'AGENDADO' ? 'selected' : '' }}>🕒 AGENDADO
                                </option>
                                <option value="CONFIRMADO" {{ $statusAtual == 'CONFIRMADO' ? 'selected' : '' }}>✅ CONFIRMADO
                                </option>
                                <option value="EM_ATENDIMENTO" {{ $statusAtual == 'EM_ATENDIMENTO' ? 'selected' : '' }}>🩺 EM
                                    ATENDIMENTO</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-8">
                            <label for="dentista_id"
                                class="form-label text-secondary small text-uppercase font-weight-bold">Dentista /
                                Profissional</label>
                            <select id="dentista_id" name="dentista_id" required
                                class="form-select py-2 @error('dentista_id') is-invalid @enderror">
                                <option value="">Selecione o Dentista</option>
                                @foreach($dentistas as $dentista)
                                    <option value="{{ $dentista->id }}" {{ old('dentista_id', $isEdit ? $agendamento->dentista_id : '') == $dentista->id ? 'selected' : '' }}>
                                        Dr(a). {{ $dentista->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dentista_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="procedimento_id"
                                class="form-label text-secondary small text-uppercase font-weight-bold">Procedimento</label>
                            <select id="procedimento_id" name="procedimento_id"
                                class="form-select py-2 @error('procedimento_id') is-invalid @enderror">
                                <option value="">Opcional</option>
                                @foreach($procedimentos as $procedimento)
                                    <option value="{{ $procedimento->id }}" {{ old('procedimento_id', $isEdit ? $agendamento->procedimento_id : '') == $procedimento->id ? 'selected' : '' }}>
                                        ✨ {{ $procedimento->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('procedimento_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="observacao"
                                class="form-label text-secondary small text-uppercase font-weight-bold">Observação</label>
                            <textarea id="observacao" name="observacao" rows="3" maxlength="45"
                                placeholder="Notas rápidas (máx. 45 caracteres)..."
                                class="form-control @error('observacao') is-invalid @enderror">{{ old('observacao', $isEdit ? $agendamento->observacao : '') }}</textarea>
                            @error('observacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('agendamento.index') }}" class="btn btn-outline-secondary px-4">
                            Cancelar
                        </a>
                        <button type="submit" class="btn {{ $isEdit ? 'btn-warning text-dark' : 'btn-primary' }} px-4">
                            {{ $isEdit ? 'Atualizar Agendamento' : 'Confirmar Agendamento' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .max-w-4xl {
            max-width: 850px;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }
    </style>
@endsection