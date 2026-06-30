@extends('dentista.layout.paginaDentista')

@section('pagina')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0 fs-5">
                            <i class="bi {{ isset($paciente) ? 'bi-pencil-square' : 'bi-person-plus' }} me-2"></i>
                            {{ isset($paciente) ? 'Editar Paciente' : 'Cadastrar Paciente' }}
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <h5 class="alert-heading fs-6 fw-bold">Por favor, corrija os erros abaixo:</h5>
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Define a URL e o Método corretos dinamicamente --}}
                        <form action="{{ isset($paciente) ? url('/dentista/paciente/' . $paciente->id) : url('/dentista/paciente') }}" method="POST">
                            @csrf
                            @if(isset($paciente))
                                @method('PUT') {{-- Diretiva necessária para o update no Laravel --}}
                            @endif

                            {{-- 1. IDENTIFICAÇÃO BÁSICA --}}
                            <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                                1. Identificação Básica
                            </h5>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="nome" class="form-label fw-semibold text-secondary small">Nome <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $paciente->nome ?? '') }}" required>
                                    @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="sobrenome" class="form-label fw-semibold text-secondary small">Sobrenome <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sobrenome') is-invalid @enderror" id="sobrenome" name="sobrenome" value="{{ old('sobrenome', $paciente->sobrenome ?? '') }}" required>
                                    @error('sobrenome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="cpf" class="form-label fw-semibold text-secondary small">CPF <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mask-cpf @error('cpf') is-invalid @enderror" id="cpf" name="cpf" value="{{ old('cpf', $paciente->cpf ?? '') }}" maxlength="14" required placeholder="000.000.000-00">
                                    @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="rg" class="form-label fw-semibold text-secondary small">RG <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('rg') is-invalid @enderror" id="rg" name="rg" value="{{ old('rg', $paciente->rg ?? '') }}" maxlength="14" required>
                                    @error('rg') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="data_nascimento" class="form-label fw-semibold text-secondary small">Data de Nascimento <span class="text-danger">*</span></label>
                                    {{-- Se for objeto Carbon, formata para 'Y-m-d', senão usa string direta --}}
                                    <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento', isset($paciente->data_nascimento) ? (is_object($paciente->data_nascimento) ? $paciente->data_nascimento->format('Y-m-d') : substr($paciente->data_nascimento, 0, 10)) : '') }}" required>
                                    @error('data_nascimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="sexo" class="form-label fw-semibold text-secondary small">Sexo</label>
                                    <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                                        <option value="">Selecione</option>
                                        <option value="Masculino" {{ old('sexo', $paciente->sexo ?? '') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Feminino" {{ old('sexo', $paciente->sexo ?? '') == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                                        <option value="Outro" {{ old('sexo', $paciente->sexo ?? '') == 'Outro' ? 'selected' : '' }}>Outro</option>
                                    </select>
                                    @error('sexo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="text-muted my-4 opacity-25">

                            {{-- 2. MEIOS DE CONTATO --}}
                            <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                                2. Meios de Contato
                            </h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="telefone" class="form-label fw-semibold text-secondary small">Telefone Residencial/Fixo <span class="text-danger">*</span></label>
                                    {{-- Como contatosPaciente() retorna HasMany, pegamos o primeiro item da Collection se houver --}}
                                    <input type="text" class="form-control mask-telefone @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone', $paciente->contatosPaciente->first()->telefone ?? '') }}" required placeholder="(00) 0000-0000">
                                    @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="whatsapp" class="form-label fw-semibold text-secondary small">WhatsApp / Celular <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mask-celular @error('whatsapp') is-invalid @enderror" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $paciente->contatosPaciente->first()->whatsapp ?? '') }}" required placeholder="(00) 00000-0000">
                                    @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="email" class="form-label fw-semibold text-secondary small">E-mail <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $paciente->contatosPaciente->first()->email ?? '') }}" required placeholder="paciente@exemplo.com">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="text-muted my-4 opacity-25">

                            {{-- 3. LOCALIZAÇÃO RESIDENCIAL --}}
                            <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                                3. Localização Residencial / Comercial
                            </h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label for="cep" class="form-label fw-semibold text-secondary small">CEP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mask-cep @error('cep') is-invalid @enderror" id="cep" name="cep" value="{{ old('cep', $paciente->enderecoPaciente->cep ?? '') }}" required placeholder="00000-000">
                                    @error('cep') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div id="cep-error" class="text-danger small mt-1 d-none">CEP não encontrado.</div>
                                </div>

                                <div class="col-md-3">
                                    <label for="pais" class="form-label fw-semibold text-secondary small">País <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('pais') is-invalid @enderror" id="pais" name="pais" value="{{ old('pais', $paciente->enderecoPaciente->pais ?? 'Brasil') }}" required placeholder="Ex: Brasil">
                                    @error('pais') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="estado" class="form-label fw-semibold text-secondary small">Estado <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" value="{{ old('estado', $paciente->enderecoPaciente->estado ?? '') }}" required placeholder="Ex: SP">
                                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="cidade" class="form-label fw-semibold text-secondary small">Cidade <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade" name="cidade" value="{{ old('cidade', $paciente->enderecoPaciente->cidade ?? '') }}" required placeholder="Ex: São Paulo">
                                    @error('cidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="bairro" class="form-label fw-semibold text-secondary small">Bairro <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro" name="bairro" value="{{ old('bairro', $paciente->enderecoPaciente->bairro ?? '') }}" required placeholder="Ex: Centro">
                                    @error('bairro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-5">
                                    <label for="rua" class="form-label fw-semibold text-secondary small">Rua <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('rua') is-invalid @enderror" id="rua" name="rua" value="{{ old('rua', $paciente->enderecoPaciente->rua ?? '') }}" required placeholder="Ex: Rua Exemplo">
                                    @error('rua') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="numero" class="form-label fw-semibold text-secondary small">Número <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero" value="{{ old('numero', $paciente->enderecoPaciente->numero ?? '') }}" required placeholder="Ex: 123">
                                    @error('numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="text-muted my-4 opacity-25">

                            {{-- 4. HISTÓRICO MÉDICO --}}
                            <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                                4. Histórico Médico
                            </h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="alergias" class="form-label fw-semibold text-secondary small">Alergias</label>
                                    <input type="text" class="form-control @error('alergias') is-invalid @enderror" id="alergias" name="alergias" value="{{ old('alergias', $paciente->historicoMedico->alergias ?? '') }}" placeholder="Ex: Penicilina">
                                    @error('alergias') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="medicacoes" class="form-label fw-semibold text-secondary small">Medicações em Uso</label>
                                    <input type="text" class="form-control @error('medicacoes') is-invalid @enderror" id="medicacoes" name="medicacoes" value="{{ old('medicacoes', $paciente->historicoMedico->medicacoes ?? '') }}" placeholder="Ex: Anti-hipertensivos">
                                    @error('medicacoes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="condicoes_cronicas" class="form-label fw-semibold text-secondary small">Condições Crônicas</label>
                                    <input type="text" class="form-control @error('condicoes_cronicas') is-invalid @enderror" id="condicoes_cronicas" name="condicoes_cronicas" value="{{ old('condicoes_cronicas', $paciente->historicoMedico->condicoes_cronicas ?? '') }}" placeholder="Ex: Diabetes, Hipertensão">
                                    @error('condicoes_cronicas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="historico_cirurgico" class="form-label fw-semibold text-secondary small">Histórico Cirúrgico</label>
                                    <textarea class="form-control @error('historico_cirurgico') is-invalid @enderror" id="historico_cirurgico" name="historico_cirurgico" rows="2" placeholder="Descreva procedimentos cirúrgicos anteriores relevantes">{{ old('historico_cirurgico', $paciente->historicoMedico->historico_cirurgico ?? '') }}</textarea>
                                    @error('historico_cirurgico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="observacoes_medicas" class="form-label fw-semibold text-secondary small">Observações Médicas Gerais</label>
                                    <textarea class="form-control @error('observacoes_medicas') is-invalid @enderror" id="observacoes_medicas" name="observacoes_medicas" rows="2" placeholder="Informações médicas adicionais importantes ou contraindicações">{{ old('observacoes_medicas', $paciente->historicoMedico->observacoes_medicas ?? '') }}</textarea>
                                    @error('observacoes_medicas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="text-muted my-4 opacity-25">

                            {{-- 5. HISTÓRICO BUCAL --}}
                            <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                                5. Histórico Bucal
                            </h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="data_ultima_visita" class="form-label fw-semibold text-secondary small">Última Visita ao Dentista</label>
                                    <input type="date" class="form-control @error('data_ultima_visita') is-invalid @enderror" id="data_ultima_visita" name="data_ultima_visita" value="{{ old('data_ultima_visita', isset($paciente->habitoSaudavel->data_ultima_visita) ? (is_object($paciente->habitoSaudavel->data_ultima_visita) ? $paciente->habitoSaudavel->data_ultima_visita->format('Y-m-d') : substr($paciente->historicoBucal->data_ultima_visita, 0, 10)) : '') }}">
                                    @error('data_ultima_visita') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="motivo_visita" class="form-label fw-semibold text-secondary small">Motivo da Visita Atual</label>
                                    <input type="text" class="form-control @error('motivo_visita') is-invalid @enderror" id="motivo_visita" name="motivo_visita" value="{{ old('motivo_visita', $paciente->historicoBucal->motivo_visita ?? '') }}" placeholder="Ex: Dor, check-up, estética">
                                    @error('motivo_visita') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="sangramento_gengival" class="form-label fw-semibold text-secondary small">Sangramento Gengival?</label>
                                    <select class="form-select @error('sangramento_gengival') is-invalid @enderror" id="sangramento_gengival" name="sangramento_gengival">
                                        <option value="">Selecione</option>
                                        <option value="Sim" {{ old('sangramento_gengival', $paciente->historicoBucal->sangramento_gengival ?? '') == 'Sim' ? 'selected' : '' }}>Sim</option>
                                        <option value="Não" {{ old('sangramento_gengival', $paciente->historicoBucal->sangramento_gengival ?? '') == 'Não' ? 'selected' : '' }}>Não</option>
                                    </select>
                                    @error('sangramento_gengival') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="sensibilidade_dental" class="form-label fw-semibold text-secondary small">Sensibilidade Dental?</label>
                                    <select class="form-select @error('sensibilidade_dental') is-invalid @enderror" id="sensibilidade_dental" name="sensibilidade_dental">
                                        <option value="">Selecione</option>
                                        <option value="Sim" {{ old('sensibilidade_dental', $paciente->historicoBucal->sensibilidade_dental ?? '') == 'Sim' ? 'selected' : '' }}>Sim</option>
                                        <option value="Não" {{ old('sensibilidade_dental', $paciente->historicoBucal->sensibilidade_dental ?? '') == 'Não' ? 'selected' : '' }}>Não</option>
                                    </select>
                                    @error('sensibilidade_dental') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="uso_protese" class="form-label fw-semibold text-secondary small">Uso de Próteses / Aparelhos?</label>
                                    <select class="form-select @error('uso_protese') is-invalid @enderror" id="uso_protese" name="uso_protese">
                                        <option value="">Selecione</option>
                                        <option value="Sim" {{ old('uso_protese', $paciente->historicoBucal->uso_protese ?? '') == 'Sim' ? 'selected' : '' }}>Sim</option>
                                        <option value="Não" {{ old('uso_protese', $paciente->historicoBucal->uso_protese ?? '') == 'Não' ? 'selected' : '' }}>Não</option>
                                    </select>
                                    @error('uso_protese') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="tipo_protese" class="form-label fw-semibold text-secondary small">Se sim, qual tipo?</label>
                                    <input type="text" class="form-control @error('tipo_protese') is-invalid @enderror" id="tipo_protese" name="tipo_protese" value="{{ old('tipo_protese', $paciente->historicoBucal->tipo_protese ?? '') }}" placeholder="Ex: Aparelho fixo, prótese parcial">
                                    @error('tipo_protese') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="tratamentos_anteriores" class="form-label fw-semibold text-secondary small">Tratamentos Bucais Anteriores</label>
                                    <textarea class="form-control @error('tratamentos_anteriores') is-invalid @enderror" id="tratamentos_anteriores" name="tratamentos_anteriores" rows="2" placeholder="Descreva restaurações, extrações, canais, etc.">{{ old('tratamentos_anteriores', $paciente->historicoBucal->tratamentos_anteriores ?? '') }}</textarea>
                                    @error('tratamentos_anteriores') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="observacoes_bucais" class="form-label fw-semibold text-secondary small">Observações Bucais Adicionais</label>
                                    <textarea class="form-control @error('observacoes_bucais') is-invalid @enderror" id="observacoes_bucais" name="observacoes_bucais" rows="2" placeholder="Queixas específicas ou percepções do paciente">{{ old('observacoes_bucais', $paciente->historicoBucal->observacoes_bucais ?? '') }}</textarea>
                                    @error('observacoes_bucais') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <hr class="text-muted my-4 opacity-25">

                            {{-- 6. HÁBITOS SAUDÁVEIS --}}
                            <h5 class="card-title text-primary fw-bold mb-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                                6. Hábitos Saudáveis
                            </h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="frequencia_escovacao" class="form-label fw-semibold text-secondary small">Frequência de Escovação</label>
                                    <select class="form-select @error('frequencia_escovacao') is-invalid @enderror" id="frequencia_escovacao" name="frequencia_escovacao">
                                        <option value="">Selecione</option>
                                        <option value="1 vez ao dia" {{ old('frequencia_escovacao', $paciente->historicoBucal->frequencia_escovacao ?? '') == '1 vez ao dia' ? 'selected' : '' }}>1 vez ao dia</option>
                                        <option value="2 vezes ao dia" {{ old('frequencia_escovacao', $paciente->historicoBucal->frequencia_escovacao ?? '') == '2 vezes ao dia' ? 'selected' : '' }}>2 vezes ao dia</option>
                                        <option value="3 ou mais vezes ao dia" {{ old('frequencia_escovacao', $paciente->historicoBucal->frequencia_escovacao ?? '') == '3 ou mais vezes ao dia' ? 'selected' : '' }}>3 ou mais vezes ao dia</option>
                                    </select>
                                    @error('frequencia_escovacao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="usa_fio_dental" class="form-label fw-semibold text-secondary small">Usa Fio Dental?</label>
                                    <select class="form-select @error('usa_fio_dental') is-invalid @enderror" id="usa_fio_dental" name="usa_fio_dental">
                                        <option value="">Selecione</option>
                                        <option value="Sim" {{ old('usa_fio_dental', $paciente->historicoBucal->usa_fio_dental ?? '') == 'Sim' ? 'selected' : '' }}>Sim</option>
                                        <option value="Não" {{ old('usa_fio_dental', $paciente->historicoBucal->usa_fio_dental ?? '') == 'Não' ? 'selected' : '' }}>Não</option>
                                        <option value="Às vezes" {{ old('usa_fio_dental', $paciente->historicoBucal->usa_fio_dental ?? '') == 'Às vezes' ? 'selected' : '' }}>Às vezes</option>
                                    </select>
                                    @error('usa_fio_dental') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="habito_fumo" class="form-label fw-semibold text-secondary small">Hábito de Fumo</label>
                                    <select class="form-select @error('habito_fumo') is-invalid @enderror" id="habito_fumo" name="habito_fumo">
                                        <option value="">Selecione</option>
                                        <option value="Não fuma" {{ old('habito_fumo', $paciente->historicoBucal->habito_fumo ?? '') == 'Não fuma' ? 'selected' : '' }}>Não fuma</option>
                                        <option value="Ex-fumante" {{ old('habito_fumo', $paciente->historicoBucal->habito_fumo ?? '') == 'Ex-fumante' ? 'selected' : '' }}>Ex-fumante</option>
                                        <option value="Fuma ocasionalmente" {{ old('habito_fumo', $paciente->historicoBucal->habito_fumo ?? '') == 'Fuma ocasionalmente' ? 'selected' : '' }}>Fuma ocasionalmente</option>
                                        <option value="Fumante regular" {{ old('habito_fumo', $paciente->historicoBucal->habito_fumo ?? '') == 'Fumante regular' ? 'selected' : '' }}>Fumante regular</option>
                                    </select>
                                    @error('habito_fumo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="consumo_alcool" class="form-label fw-semibold text-secondary small">Consumo de Álcool</label>
                                    <select class="form-select @error('consumo_alcool') is-invalid @enderror" id="consumo_alcool" name="consumo_alcool">
                                        <option value="">Selecione</option>
                                        <option value="Não consome" {{ old('consumo_alcool', $paciente->historicoBucal->consumo_alcool ?? '') == 'Não consome' ? 'selected' : '' }}>Não consome</option>
                                        <option value="Raramente" {{ old('consumo_alcool', $paciente->historicoBucal->consumo_alcool ?? '') == 'Raramente' ? 'selected' : '' }}>Raramente</option>
                                        <option value="Moderadamente" {{ old('consumo_alcool', $paciente->historicoBucal->consumo_alcool ?? '') == 'Moderadamente' ? 'selected' : '' }}>Moderadamente</option>
                                        <option value="Frequentemente" {{ old('consumo_alcool', $paciente->historicoBucal->consumo_alcool ?? '') == 'Frequentemente' ? 'selected' : '' }}>Frequentemente</option>
                                    </select>
                                    @error('consumo_alcool') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="pratica_atividade_fisica" class="form-label fw-semibold text-secondary small">Atividade Física</label>
                                    <select class="form-select @error('pratica_atividade_fisica') is-invalid @enderror" id="pratica_atividade_fisica" name="pratica_atividade_fisica">
                                        <option value="">Selecione</option>
                                        <option value="Não pratica" {{ old('pratica_atividade_fisica', $paciente->historicoBucal->pratica_atividade_fisica ?? '') == 'Não pratica' ? 'selected' : '' }}>Não pratica</option>
                                        <option value="1-2 vezes por semana" {{ old('pratica_atividade_fisica', $paciente->historicoBucal->pratica_atividade_fisica ?? '') == '1-2 vezes por semana' ? 'selected' : '' }}>1-2 vezes por semana</option>
                                        <option value="3-4 vezes por semana" {{ old('pratica_atividade_fisica', $paciente->historicoBucal->pratica_atividade_fisica ?? '') == '3-4 vezes por semana' ? 'selected' : '' }}>3-4 vezes por semana</option>
                                        <option value="Diariamente" {{ old('pratica_atividade_fisica', $paciente->historicoBucal->pratica_atividade_fisica ?? '') == 'Diariamente' ? 'selected' : '' }}>Diariamente</option>
                                    </select>
                                    @error('pratica_atividade_fisica') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="qualidade_sono" class="form-label fw-semibold text-secondary small">Qualidade do Sono</label>
                                    <select class="form-select @error('qualidade_sono') is-invalid @enderror" id="qualidade_sono" name="qualidade_sono">
                                        <option value="">Selecione</option>
                                        <option value="Boa" {{ old('qualidade_sono', $paciente->historicoBucal->qualidade_sono ?? '') == 'Boa' ? 'selected' : '' }}>Boa</option>
                                        <option value="Razoável" {{ old('qualidade_sono', $paciente->historicoBucal->qualidade_sono ?? '') == 'Razoável' ? 'selected' : '' }}>Razoável</option>
                                        <option value="Ruim" {{ old('qualidade_sono', $paciente->historicoBucal->qualidade_sono ?? '') == 'Ruim' ? 'selected' : '' }}>Ruim</option>
                                    </select>
                                    @error('qualidade_sono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="observacoes_habitos" class="form-label fw-semibold text-secondary small">Observações sobre Estilo de Vida</label>
                                    <textarea class="form-control @error('observacoes_habitos') is-invalid @enderror" id="observacoes_habitos" name="observacoes_habitos" rows="2" placeholder="Outros hábitos relevantes (ex: bruxismo, roer unhas, dieta rica em açúcar)">{{ old('observacoes_habitos', $paciente->historicoBucal->observacoes_habitos ?? '') }}</textarea>
                                    @error('observacoes_habitos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- BOTÕES DE AÇÃO --}}
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                                <a href="{{ url('/dentista/paciente') }}" class="btn btn-light border">
                                    <i class="bi bi-arrow-left me-1"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-lg me-1"></i> {{ isset($paciente) ? 'Atualizar Paciente' : 'Salvar Paciente' }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection