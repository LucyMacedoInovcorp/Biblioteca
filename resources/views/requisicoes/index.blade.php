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
              <th>📷 Livro</th>
              <th>👤 Foto</th>
              <th>👤 Usuário</th>
              <th>📅 Data Requisição</th>
              <th>📅 Data Devolução</th>
              <th>⏳ Dias Decorridos</th>
              <th>📌 Status</th>
              <th>📗 Disponibilidade</th> 
              <th class="@if(!auth()->check() || !auth()->user()->is_admin) hidden @endif">⚙️ Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($requisicoes as $req)
            <tr class="hover">
              <!-- Livro -->
              <td>{{ $req->livro->nome ?? '—' }}</td>

              <!-- Foto -->
              <td>
                @if($req->user && $req->user->profile_photo_path)
                  <img src="{{ $req->user->profile_photo_url }}" class="w-12 h-12 rounded-full">
                @endif
              </td>

              <!-- Usuário -->
              <td>{{ $req->user->name ?? '—' }}</td>

              <!-- Data da requisição -->
              <td>{{ $req->created_at->format('d/m/Y') }}</td>

              <!-- Data de devolução -->
              <td>{{ $req->data_recepcao ? $req->data_recepcao->format('d/m/Y') : '—' }}</td>

              <!-- Dias decorridos -->
              <td>
                <span class="badge badge-outline">
                  {{ $req->dias_decorridos }} {{ \Illuminate\Support\Str::plural('dia', $req->dias_decorridos) }}
                </span>
              </td>

              <!-- Status -->
              <td>
                @if($req->ativo)
                  <span class="badge badge-success">Ativo</span>
                @else
                  <span class="badge badge-error">Finalizado</span>
                @endif
              </td>

              <!-- Disponibilidade -->
              <td>
                @if($req->livro)
                  <span class="badge {{ $req->livro->disponivel ? 'badge-success' : 'badge-error' }}">
                    {{ $req->livro->disponivel ? '🟢 Disponível' : '🔴 Indisponível' }}
                  </span>
                @else
                  —
                @endif
              </td>

              <!-- Botão confirmar devolução -->
              <td>
                @if($req->ativo)
                  <form action="{{ route('requisicoes.confirmar', $req->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-primary">Confirmar Devolução</button>
                  </form>
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
