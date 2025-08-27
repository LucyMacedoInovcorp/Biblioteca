@extends('layouts.main')
@section('title', 'Requisições')
@section('content')

<div id="requisicoes-container" class="w-full p-6">
  <!-- Título -->
  <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
    <h2 class="text-3xl font-bold flex items-center gap-2">
      📑 Requisições Ativas
      <span class="badge badge-primary badge-lg">{{ $requisicoes->count() ?? 0 }}</span>
    </h2>
  </div>

  <!-- Tabela -->
  <div class="card bg-base-100 shadow-md mt-10">
    <div class="card-body p-0">
      <div class="overflow-x-auto">
        <table class="myTable table table-zebra w-full">
          <thead class="bg-base-200">
            <tr>
              <th>📷 Imagem</th>
              <th>📚 Livro</th>
              <th>👤 Usuário</th>
              <th>🔢 ISBN</th>
              <th>📅 Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requisicoes as $req)
              <tr class="hover">
                <!-- Capa do livro -->
                <td>
                  @if($req->livro && $req->livro->imagemcapa)
                    <img src="{{ asset($req->livro->imagemcapa) }}" alt="{{ $req->livro->nome }}"
                      class="w-12 h-12 object-cover rounded-md shadow-sm">
                  @else
                    <span class="badge badge-ghost">Sem capa</span>
                  @endif
                </td>

                <!-- Nome do livro -->
                <td class="font-semibold text-primary">
                  {{ $req->livro->nome ?? '—' }}
                </td>

                <!-- Nome do usuário -->
                <td>{{ $req->user->name ?? '—' }}</td>

                <!-- ISBN -->
                <td>{{ $req->livro->ISBN ?? '—' }}</td>

                <!-- Status -->
                <td>
                  @if($req->ativo)
                    <span class="badge badge-success">Ativo</span>
                  @else
                    <span class="badge badge-error">Finalizado</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
