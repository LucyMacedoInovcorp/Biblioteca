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

            <!-- Reviews ativos -->
            <div class="card bg-base-100 shadow-md mt-8">
                <div class="card-body">
                    <h3 class="text-2xl font-semibold mb-4">📝 Avaliações</h3>
                    @php
                    $reviewsAtivos = $livro->avaliacoes->where('status', 'ativo');
                    @endphp
                    @if($reviewsAtivos->count())
                    <ul class="space-y-4">
                        @foreach($reviewsAtivos as $review)
                        <li class="border-b pb-2">
                            <div class="font-semibold text-lg">{{ $review->user->name ?? 'Cidadão' }}</div>
                            <div class="text-yellow-700 font-bold">Nota: {{ $review->rating }}</div>
                            <div class="mt-1">{{ $review->review }}</div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-gray-500">Nenhuma avaliação ativa para este livro.</div>
                    @endif
                </div>
            </div>


        </div>

        <div>
            <!-- Livros relacionados -->
            @if($relacionados->count())
            <div class="card bg-base-100 shadow-md mt-8">
                <div class="card-body">
                    <h3 class="text-2xl font-semibold mb-4">📚 Livros Relacionados</h3>
                    <div class="flex flex-wrap gap-6 justify-start">
                        @foreach($relacionados as $rel)
                        <div class="bg-base-200 shadow rounded-lg p-2 flex flex-col items-center w-32">
                            <a href="{{ route('livros.show', $rel->id) }}" class="w-full flex flex-col items-center">
                                <img src="{{ asset($rel->imagemcapa ?? 'images/livro-default.png') }}"
                                    alt="{{ $rel->nome }}"
                                    class="w-24 h-36 object-cover mb-2 rounded">
                                <div class="text-center font-semibold text-blue-700 hover:underline text-sm truncate w-full">{{ $rel->nome }}</div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endsection