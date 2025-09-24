@extends('layouts.main')

@section('title', 'Logs do Sistema')

@section('content')
<div class="w-full p-6">
    <!-- Título -->
    <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
        <h2 class="text-3xl font-bold flex items-center gap-2">
            📋 Logs do Sistema
            <span class="badge badge-primary badge-lg">{{ $logs->total() }}</span>
        </h2>


        <!-- Filtros compactos no topo -->
        <div class="flex gap-2 flex-wrap">
            <select id="filtroModulo" class="select select-sm select-bordered text-xs min-w-32">
                <option value="" class="text-xs">📂 Módulos</option>
                @foreach($modulos as $modulo)
                <option value="{{ $modulo }}" class="text-xs" {{ request('modulo') == $modulo ? 'selected' : '' }}>
                    @switch($modulo)
                    @case('livros') 📚 @break
                    @case('autores') 👨‍💼 @break
                    @case('editoras') 🏢 @break
                    @case('requisicoes') ✅ @break
                    @case('utilizadores') 👤 @break
                    @default 📋 @break
                    @endswitch
                    {{ ucfirst($modulo) }}
                </option>
                @endforeach
            </select>

            <select id="filtroUsuario" class="select select-sm select-bordered text-xs min-w-40">
                <option value="" class="text-xs">👥 Utilizadores</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" class="text-xs" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                    {{ $usuario->name }} {{ $usuario->is_admin ? '👑' : '' }}
                </option>
                @endforeach
            </select>
        </div>
    </div>





    <!-- Tabela -->
    <div class="card bg-base-100 shadow-md">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="myTable table table-zebra w-full">
                    <thead class="bg-base-200">
                        <tr>
                            <th scope="col" class="w-32">🕒 Data/Hora</th>
                            <th scope="col" class="w-40">👤 Utilizador</th>
                            <th scope="col" class="w-24">📂 Módulo</th>
                            <th scope="col" class="w-16">🆔 ID</th>
                            <th scope="col" class="min-w-96">📝 Alteração</th>
                            <th scope="col" class="w-32">🌐 IP</th>
                            <th scope="col" class="w-40">🖥️ Browser</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr class="hover">
                            <!-- Data/Hora -->
                            <td>
                                <div class="text-sm">
                                    <div class="font-semibold">{{ $log->data_hora->format('d/m/Y') }}</div>
                                    <div class="text-gray-500">{{ $log->data_hora->format('H:i:s') }}</div>
                                </div>
                            </td>

                            <!-- Utilizador -->
                            <td>
                                @if($log->user)
                                <div class="flex items-center gap-2">
                                    <div class="avatar placeholder">
                                        <div class="bg-blue-200 text-blue-900 rounded-full w-8 h-8 flex items-center justify-center text-xs">
                                            {{ $log->user->is_admin ? '👑' : '👤' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">{{ $log->user->name }}</div>
                                    </div>
                                </div>
                                @else
                                <span class="badge badge-ghost">🤖 Sistema</span>
                                @endif
                            </td>

                            <!-- Módulo -->
                            <td>
                                <span class="text-sm flex justify-center items-center">{{ ucfirst($log->modulo) }}</span>
                            </td>

                            <!-- ID do Objeto -->
                            <td>
                                @if($log->objeto_id)
                                <span class="badge badge-outline badge-sm">#{{ $log->objeto_id }}</span>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>



                            <!-- Alteração -->
                            <td class="max-w-lg"> <!-- Mudou de max-w-xs para max-w-lg -->
                                @if(Str::contains($log->alteracao, '→'))
                                @php
                                $parts = explode(' → ', $log->alteracao, 2);
                                $tipoAcao = trim($parts[0] ?? '');
                                $detalhes = trim($parts[1] ?? '');
                                @endphp

                                <div class="space-y-1">
                                    <!-- Tipo de acção -->
                                    <div class="flex items-center gap-1">
                                        @switch($tipoAcao)
                                        @case('Criação')
                                        <span class="badge badge-success badge-xs">➕</span>
                                        <span class="font-semibold text-success text-sm">{{ $tipoAcao }}</span>
                                        @break
                                        @case('Actualização')
                                        <span class="badge badge-warning badge-xs">✏️</span>
                                        <span class="font-semibold text-warning text-sm">{{ $tipoAcao }}</span>
                                        @break
                                        @case('Exclusão')
                                        <span class="badge badge-error badge-xs">🗑️</span>
                                        <span class="font-semibold text-error text-sm">{{ $tipoAcao }}</span>
                                        @break
                                        @default
                                        <span class="badge badge-neutral badge-xs">ℹ️</span>
                                        <span class="font-semibold text-neutral text-sm">{{ $tipoAcao }}</span>
                                        @endswitch
                                    </div>

                                    <!-- Detalhes -->
                                    @if($detalhes)
                                    <div class="text-xs text-gray-600">
                                        @if(Str::contains($detalhes, ' | '))
                                        @php
                                        $mudancas = explode(' | ', $detalhes);
                                        @endphp
                                        @foreach(array_slice($mudancas, 0, 3) as $mudanca) <!-- Aumentou de 2 para 3 -->
                                        <div class="bg-base-200 px-3 py-2 rounded text-xs mb-2"> <!-- Aumentou padding -->
                                            @if(Str::contains($mudanca, ' → de:') && Str::contains($mudanca, 'para:'))
                                            @php
                                            if (preg_match('/(.+?) → de: (.+?) para: (.+)/', $mudanca, $matches)) {
                                            $campo = trim($matches[1]);
                                            $antes = trim($matches[2]);
                                            $depois = trim($matches[3]);
                                            } else {
                                            $campo = $mudanca;
                                            $antes = '';
                                            $depois = '';
                                            }
                                            @endphp
                                            @if($campo && $antes && $depois)
                                            <div class="font-medium text-primary text-xs mb-2">📝 {{ $campo }}</div>
                                            <div class="grid grid-cols-1 gap-2"> <!-- Mudou de grid-cols-2 para grid-cols-1 para mais espaço -->
                                                <div class="text-red-600 text-xs flex items-start gap-1">
                                                    <span class="flex-shrink-0">❌ Antes:</span>
                                                    <span class="break-words" title="{{ $antes }}">{{ Str::limit($antes, 60) }}</span> <!-- Aumentou de 15 para 60 -->
                                                </div>
                                                <div class="text-green-600 text-xs flex items-start gap-1">
                                                    <span class="flex-shrink-0">✅ Depois:</span>
                                                    <span class="break-words" title="{{ $depois }}">{{ Str::limit($depois, 60) }}</span> <!-- Aumentou de 15 para 60 -->
                                                </div>
                                            </div>
                                            @else
                                            <div class="text-xs">{{ Str::limit($mudanca, 80) }}</div> <!-- Aumentou de 40 para 80 -->
                                            @endif
                                            @else
                                            <div class="text-xs">{{ Str::limit($mudanca, 80) }}</div> <!-- Aumentou de 40 para 80 -->
                                            @endif
                                        </div>
                                        @endforeach
                                        @if(count($mudancas) > 3) <!-- Ajustou de 2 para 3 -->
                                        <div class="text-xs text-gray-400 italic">
                                            +{{ count($mudancas) - 3 }} mais alterações...
                                        </div>
                                        @endif
                                        @else
                                        <div class="italic text-xs font-medium bg-base-200 px-3 py-2 rounded">
                                            {{ Str::limit($detalhes, 100) }} <!-- Aumentou de 50 para 100 -->
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                @else
                                <div class="font-semibold text-sm">{{ Str::limit($log->alteracao, 120) }}</div> <!-- Aumentou de 80 para 120 -->
                                @endif
                            </td>

                            <!-- IP -->
                            <td>
                                <div class="text-xs font-mono">
                                    {{ $log->ip }}
                                    @if($log->ip == request()->ip())
                                    <span title="Seu IP atual"></span>
                                    @endif
                                </div>
                            </td>


                            <!-- Browser -->
                            <td>
                                <div class="text-xs text-gray-500 flex items-center gap-1" title="{{ $log->browser }}">
                                    @php
                                    $browserInfo = strtolower($log->browser);
                                    @endphp

                                    @if(Str::contains($browserInfo, 'chrome'))
                                    <span class="text-yellow-600" title="Google Chrome">🌐</span>
                                    @elseif(Str::contains($browserInfo, 'firefox'))
                                    <span class="text-orange-600" title="Mozilla Firefox">🦊</span>
                                    @elseif(Str::contains($browserInfo, 'safari'))
                                    <span class="text-blue-600" title="Safari">🧭</span>
                                    @elseif(Str::contains($browserInfo, 'edge'))
                                    <span class="text-blue-800" title="Microsoft Edge">📐</span>
                                    @elseif(Str::contains($browserInfo, 'opera'))
                                    <span class="text-red-600" title="Opera">🎭</span>
                                    @else
                                    <span title="Browser Desconhecido">🖥️</span>
                                    @endif

                                    <span class="truncate max-w-32">
                                        @if(Str::contains($browserInfo, 'chrome'))
                                        Chrome
                                        @elseif(Str::contains($browserInfo, 'firefox'))
                                        Firefox
                                        @elseif(Str::contains($browserInfo, 'safari'))
                                        Safari
                                        @elseif(Str::contains($browserInfo, 'edge'))
                                        Edge
                                        @elseif(Str::contains($browserInfo, 'opera'))
                                        Opera
                                        @else
                                        {{ Str::limit($log->browser, 25) }}
                                        @endif
                                    </span>
                                </div>
                            </td>




                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginação (se necessário) -->
    @if($logs->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $logs->links() }}
    </div>
    @endif
</div>

<!-- Modal para Detalhes -->
<div class="modal" id="detalhesModal">
    <div class="modal-box w-11/12 max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">📋 Detalhes do Log</h3>
            <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('detalhesModal').close()">✕</button>
        </div>
        <div id="detalhesContent">
            <!-- Conteúdo carregado via JavaScript -->
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar DataTables com configuração similar ao livros.blade.php
        $('.myTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json"
            },
            "pageLength": 25,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "Todos"]
            ],
            "order": [
                [0, "desc"]
            ],
            "columnDefs": [{
                    "orderable": false,
                    "targets": [7]
                },
                {
                    "searchable": true,
                    "targets": "_all"
                }
            ],
            "responsive": true,
            "processing": true,
            "dom": 'lBfrtip',
            "buttons": [{
                extend: 'excel',
                text: '📊 Excel',
                className: 'btn btn-success btn-sm mx-1',
                title: 'Logs do Sistema - ' + new Date().toLocaleDateString('pt-BR')
            }]
        });

        // Filtros automáticos
        $('#filtroModulo').change(function() {
            const modulo = $(this).val();
            if (modulo) {
                window.location.href = '{{ route("admin.logs.index") }}?modulo=' + modulo;
            } else {
                window.location.href = '{{ route("admin.logs.index") }}';
            }
        });

        $('#filtroUsuario').change(function() {
            const userId = $(this).val();
            if (userId) {
                window.location.href = '{{ route("admin.logs.index") }}?user_id=' + userId;
            } else {
                window.location.href = '{{ route("admin.logs.index") }}';
            }
        });
    });

    // Função para ver detalhes (usando modal do DaisyUI)
    function verDetalhes(logId) {
        document.getElementById('detalhesModal').showModal();
        document.getElementById('detalhesContent').innerHTML = '<div class="text-center"><span class="loading loading-spinner loading-lg"></span></div>';

        // Simular carregamento de detalhes
        setTimeout(() => {
            document.getElementById('detalhesContent').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <h4 class="font-semibold text-primary">Informações Básicas</h4>
                    <div class="bg-base-200 p-3 rounded">
                        <p><strong>ID do Log:</strong> #${logId}</p>
                        <p><strong>Data/Hora:</strong> ${new Date().toLocaleString('pt-BR')}</p>
                        <p><strong>Tipo de Ação:</strong> <span class="badge badge-info">Atualização</span></p>
                    </div>
                </div>
                <div class="space-y-2">
                    <h4 class="font-semibold text-primary">Informações Técnicas</h4>
                    <div class="bg-base-200 p-3 rounded">
                        <p><strong>User Agent:</strong></p>
                        <code class="text-xs block mt-1 p-2 bg-base-300 rounded">Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36...</code>
                    </div>
                </div>
            </div>
        `;
        }, 1000);
    }
</script>

<style>
    /* Estilos customizados para manter consistência com livros.blade.php */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin: 0.5rem 0;
    }

    .dt-buttons {
        margin-bottom: 1rem;
    }

    .dt-buttons .btn {
        margin-right: 0.25rem;
    }

    .table th {
        background-color: hsl(var(--b2));
        font-weight: 600;
        font-size: 0.875rem;
    }

    .modal-box {
        background-color: hsl(var(--b1));
    }
</style>
@endsection