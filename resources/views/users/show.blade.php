@extends('layouts.main')

@section('title', 'Detalhes do Cidadão')

@section('content')
<div class="w-full p-6">

    <!-- Info do cidadão -->
    <div class="card bg-base-100 shadow-lg mb-8">
        <div class="card-body">
            <h2 class="text-3xl font-bold mb-4">{{ $user->name }}</h2>

            <p><strong>📧 Email:</strong> {{ $user->email }}</p>

            @if($user->profile_photo_url ?? false)
                <p><img src="{{ $user->profile_photo_url }}" class="w-20 h-20 rounded-full mt-2"></p>
            @endif
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
                            <th>📚 Livro</th>
                            <th>📅 Data Requisição</th>
                            <th>📅 Data Devolução</th>
                            <th>📌 Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->requisicoes as $req)
                        <tr>
                            <td>{{ $req->numero }}</td>
                            <td>{{ $req->livro->nome ?? '—' }}</td>
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
                            <td colspan="5" class="text-center py-4">Nenhuma requisição encontrada para este cidadão.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
