@extends('dentista.layout.paginaDentista')

@section('pagina')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fs-5"><i class="bi bi-people me-2"></i>Pacientes Cadastrados</h4>
                        <a href="{{ route('paciente.create') }}" class="btn btn-light btn-sm fw-semibold">
                            <i class="bi bi-person-plus me-1"></i> Novo Paciente
                        </a>
                    </div>
                    <div class="card-body p-4">

                        {{-- Mensagens de Sucesso --}}
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Tabela de Pacientes --}}
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light text-secondary small text-uppercase">
                                    <tr>
                                        <th scope="col" class="ps-3">Nome Completo</th>
                                        <th scope="col">CPF / RG</th>
                                        <th scope="col">Contato</th>
                                        <th scope="col">Localização</th>
                                        <th scope="col" class="text-center" style="width: 140px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @forelse ($pacientes as $paciente)
                                        <tr>
                                            <td class="ps-3 fw-semibold text-dark">
                                                {{ $paciente->nome }} {{ $paciente->sobrenome }}
                                                <div class="text-muted fw-normal x-small" style="font-size: 0.75rem;">
                                                    Nasc: {{ \Carbon\Carbon::parse($paciente->data_nascimento)->format('d/m/Y') }} 
                                                    ({{ $paciente->sexo ?? 'Não informado' }})
                                                </div>
                                            </td>
                                            <td>
                                                <span class="d-block">{{ $paciente->cpf }}</span>
                                                <span class="text-muted text-xs">RG: {{ $paciente->rg }}</span>
                                            </td>
                                            <td>
                                                <span class="d-block"><i class="bi bi-whatsapp text-success me-1"></i>{{ $paciente->whatsapp }}</span>
                                                <span class="text-muted text-xs">{{ $paciente->email }}</span>
                                            </td>
                                            <td>
                                                <span>{{ $paciente->enderecoPaciente->cidade ?? 'N/A' }} - {{ $paciente->estado ?? 'N/A' }}</span>
                                                <span class="text-muted d-block text-xs">Bairro: {{ $paciente->enderecoPaciente->bairro ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    {{-- Botão Visualizar Detalhes (Abre o Modal) --}}
                                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalPaciente{{ $paciente->id }}" title="Visualizar Prontuário">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    
                                                    {{-- Botão Editar --}}
                                                    <a href="{{ route('paciente.edit', $paciente->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    {{-- Botão Excluir --}}
                                                    <form action="{{ route('paciente.destroy', $paciente->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este paciente?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- MODAL DE DETALHES DO PACIENTE --}}
                                        <div class="modal fade" id="modalPaciente{{ $paciente->id }}" tabindex="-1" aria-labelledby="modalPacienteLabel{{ $paciente->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-light border-bottom">
                                                        <h5 class="modal-title text-primary fw-bold fs-6" id="modalPacienteLabel{{ $paciente->id }}">
                                                            <i class="bi bi-person-vcard me-2"></i>Prontuário: {{ $paciente->nome }} {{ $paciente->sobrenome }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 small">
                                                        
                                                        {{-- 1 & 2. Identificação e Contato --}}
                                                        <h6 class="text-primary fw-bold mb-2 border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.8rem;">1 & 2. Dados Pessoais e Contato</h6>
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-4"><strong>Data de Nascimento:</strong> {{ \Carbon\Carbon::parse($paciente->data_nascimento)->format('d/m/Y') }}</div>
                                                            <div class="col-md-4"><strong>Sexo:</strong> {{ $paciente->sexo ?? 'Não informado' }}</div>
                                                            <div class="col-md-4"><strong>Telefone Fixo:</strong> {{ $paciente->telefone }}</div>
                                                            <div class="col-md-4"><strong>Celular/WhatsApp:</strong> {{ $paciente->whatsapp }}</div>
                                                            <div class="col-md-8"><strong>E-mail:</strong> {{ $paciente->email }}</div>
                                                        </div>

                                                        {{-- 3. Localização --}}
                                                        <h6 class="text-primary fw-bold mb-2 border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.8rem;">3. Endereço</h6>
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-3"><strong>CEP:</strong> {{ $paciente->cep }}</div>
                                                            <div class="col-md-6"><strong>Rua:</strong> {{ $paciente->rua }}, Nº {{ $paciente->numero }}</div>
                                                            <div class="col-md-3"><strong>Bairro:</strong> {{ $paciente->bairro }}</div>
                                                            <div class="col-md-4"><strong>Cidade/UF:</strong> {{ $paciente->cidade }} - {{ $paciente->estado }}</div>
                                                            <div class="col-md-4"><strong>País:</strong> {{ $paciente->pais }}</div>
                                                        </div>

                                                        {{-- 4. Histórico Médico --}}
                                                        <h6 class="text-primary fw-bold mb-2 border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.8rem;">4. Histórico Médico</h6>
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-4"><strong>Alergias:</strong> <span class="{{ $paciente->alergias ? 'text-danger fw-bold' : 'text-muted' }}">{{ $paciente->alergias ?? 'Nenhuma' }}</span></div>
                                                            <div class="col-md-4"><strong>Medicações em Uso:</strong> <span class="{{ $paciente->medicacoes ? 'text-warning fw-bold' : 'text-muted' }}">{{ $paciente->medicacoes ?? 'Nenhuma' }}</span></div>
                                                            <div class="col-md-4"><strong>Condições Crônicas:</strong> <span class="{{ $paciente->condicoes_cronicas ? 'text-primary fw-bold' : 'text-muted' }}">{{ $paciente->condicoes_cronicas ?? 'Nenhuma' }}</span></div>
                                                            <div class="col-12"><strong>Histórico Cirúrgico:</strong> <p class="text-muted mb-0">{{ $paciente->historico_cirurgico ?? 'Nenhum histórico informado.' }}</p></div>
                                                            <div class="col-12"><strong>Observações Médicas Gerais:</strong> <p class="text-muted mb-0">{{ $paciente->observacoes_medicas ?? 'Sem observações.' }}</p></div>
                                                        </div>

                                                        {{-- 5. Histórico Bucal --}}
                                                        <h6 class="text-primary fw-bold mb-2 border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.8rem;">5. Histórico Bucal</h6>
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-4"><strong>Última Visita:</strong> {{ $paciente->data_ultima_visita ? \Carbon\Carbon::parse($paciente->data_ultima_visita)->format('d/m/Y') : 'Não informada' }}</div>
                                                            <div class="col-md-4"><strong>Motivo da Visita:</strong> {{ $paciente->motivo_visita ?? 'Não informado' }}</div>
                                                            <div class="col-md-4"><strong>Sangramento Gengival?</strong> {{ $paciente->sangramento_gengival ?? 'Não' }}</div>
                                                            <div class="col-md-4"><strong>Sensibilidade Dental?</strong> {{ $paciente->sensibilidade_dental ?? 'Não' }}</div>
                                                            <div class="col-md-4"><strong>Uso de Prótese/Aparelho?</strong> {{ $paciente->uso_protese ?? 'Não' }}</div>
                                                            <div class="col-md-4"><strong>Tipo de Prótese:</strong> {{ $paciente->tipo_protese ?? 'Nenhum' }}</div>
                                                            <div class="col-12"><strong>Tratamentos Anteriores:</strong> <p class="text-muted mb-0">{{ $paciente->tratamentos_anteriores ?? 'Nenhum informado.' }}</p></div>
                                                            <div class="col-12"><strong>Observações Bucais Adicionais:</strong> <p class="text-muted mb-0">{{ $paciente->observacoes_bucais ?? 'Sem observações.' }}</p></div>
                                                        </div>

                                                        {{-- 6. Hábitos Saudáveis --}}
                                                        <h6 class="text-primary fw-bold mb-2 border-bottom pb-1 text-uppercase text-xs" style="font-size: 0.8rem;">6. Hábitos & Estilo de Vida</h6>
                                                        <div class="row g-3">
                                                            <div class="col-md-4"><strong>Frequência Escovação:</strong> {{ $paciente->frequencia_escovacao ?? 'Não informado' }}</div>
                                                            <div class="col-md-4"><strong>Usa Fio Dental?</strong> {{ $paciente->usa_fio_dental ?? 'Não informado' }}</div>
                                                            <div class="col-md-4"><strong>Hábito de Fumo:</strong> {{ $paciente->habito_fumo ?? 'Não informado' }}</div>
                                                            <div class="col-md-4"><strong>Consumo de Álcool:</strong> {{ $paciente->consumo_alcool ?? 'Não informado' }}</div>
                                                            <div class="col-md-4"><strong>Atividade Física:</strong> {{ $paciente->pratica_atividade_fisica ?? 'Não informado' }}</div>
                                                            <div class="col-md-4"><strong>Qualidade do Sono:</strong> {{ $paciente->qualidade_sono ?? 'Não informado' }}</div>
                                                            <div class="col-12"><strong>Observações sobre Estilo de Vida:</strong> <p class="text-muted mb-0">{{ $paciente->observacoes_habitos ?? 'Sem observações adicionais.' }}</p></div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer bg-light border-top">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
                                                        <a href="{{ route('paciente.edit', $paciente->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i> Editar Cadastro</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- FIM DO MODAL --}}

                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="bi bi-person-x fs-4 d-block mb-2"></i>
                                                Nenhum paciente cadastrado até o momento.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginação do Laravel (Se houver) --}}
                        @if(method_exists($pacientes, 'links'))
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $pacientes->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection