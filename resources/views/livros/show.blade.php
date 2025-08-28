@extends('layouts.main')

@section('title', 'Detalhes do Livro')

@section('content')
<div class="w-full p-6">

    <!-- Info do livro -->
    <div class="card bg-base-100 shadow-lg mb-8">
        <div class="card-body">
            <h2 class="text-3xl font-bold mb-4">{{ $livro->nome }}</h2>


            <p><strong>📌 Disponibilidade:</strong>
                <span class="badge {{ $livro->disponivel ? 'badge-success' : 'badge-error' }}">
                    {{ $livro->disponivel ? '🟢 Disponível' : '🔴 Indisponível' }}
                </span>
            </p>
        </div>
    </div>

    <!-- Histórico de requisições -->
    <div class="card bg-base-100 shadow-md">
        <div class="card-body p-0">
            <h3 class="text-2xl font-semibold p-4">📑 Histórico de Requisições</h3>

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200">
                        <tr>
                            <th>🔢 Nº</th>
                            <th>👤 Cidadão</th>
                            <th>📅 Data Requisição</th>
                            <th>📅 Data Devolução</th>
                            <th>📌 Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($livro->requisicoes as $req)
                        <tr>
                            <td>{{ $req->numero }}</td>
                            <td>{{ $req->user->name ?? '—' }}</td>
                            <td>{{ $req->created_at->format('d/m/Y') }}</td>
                            <td>{{ $req->data_recepcao ? $req->data_recepcao->format('d/m/Y') : '—' }}</td>
                            <td>
                                @if($req->ativo)
                                <span class="badge badge-success">Ativo</span>
                                @else
                                <span class="badge badge-error">Finalizado</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Nenhuma requisição encontrada para este livro.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection