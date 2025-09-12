@extends('layouts.main')
@section('title', 'Requisições')
@section('content')

@if(session('success'))
  <div class="mb-4" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.75rem 1rem; border-radius: 0.375rem;">
    {{ session('success') }}
  </div>
@endif

<!-- Indicadores -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="card bg-base-100 shadow p-4 text-center">
    <h3 class="text-xl font-semibold">📌 Requisições Ativas</h3>
    <p class="text-3xl font-bold text-primary">{{ $ativas }}</p>
  </div>

  <div class="card bg-base-100 shadow p-4 text-center">
    <h3 class="text-xl font-semibold">📅 Últimos 30 dias</h3>
    <p class="text-3xl font-bold text-secondary">{{ $ultimos30dias }}</p>
  </div>

  <div class="card bg-base-100 shadow p-4 text-center">
    <h3 class="text-xl font-semibold">📗 Livros entregues Hoje</h3>
    <p class="text-3xl font-bold text-success">{{ $entreguesHoje }}</p>
  </div>
</div>


<!-- Tabela -->
<div class="card bg-base-100 shadow-md mt-10">
  <div class="card-body p-0">
    <div class="overflow-x-auto">
      <table class="myTable table table-zebra w-full">
        <thead class="bg-base-200">
          <tr>
            <th>🔢 Nº Requisição</th>
            <th>📚 Livro</th>
            <th>📷 Foto</th>
            <th>👤 Cidadão</th>
            <th>📅 Data Requisição</th>
            <th>📅 Prazo Estimado Devolução</th>
            <th>📅 Data Devolução</th>
            <th>📌 Status</th>
            <th>📗 Disponibilidade</th>
            <th class="@if(!auth()->check() || !auth()->user()->is_admin) hidden @endif">⚙️ Ações</th>
            <th class="@if(!auth()->check() || auth()->user()->is_admin) hidden @endif">✍️ Avaliação</th>
          </tr>
        </thead>
        <tbody>
          @foreach($requisicoes as $req)
          <tr class="hover">

            <!-- Número da requisição -->
            <td>{{ $req->numero }}</td>

            <!-- Livro -->
            <td>
              @if($req->livro)
              <a href="{{ route('livros.show', $req->livro->id) }}"
                class="link link-primary font-semibold hover:underline tooltip"
                data-tip="Detalhe do Livro">
                {{ $req->livro->nome }}
              </a>
              @else
              
              @endif
            </td>

            <!-- Foto -->
            <td>
              @if($req->user && $req->user->profile_photo_path)
              <img src="{{ $req->user->profile_photo_url }}?v={{ $req->user->updated_at->timestamp }}" class="w-12 h-12 rounded-full">
              @endif
            </td>

            <!-- Usuário -->
            <td>
              @if($req->user)
              <a href="{{ route('users.show', $req->user->id) }}"
                class="link link-primary hover:underline"
                title="Detalhe do Cidadão">
                {{ $req->user->name }}
              </a>
              @else
              —
              @endif
            </td>

            <!-- Data da requisição -->
            <td>{{ $req->created_at->format('d/m/Y') }}</td>
            <!-- Prazo de devolução -->
            <td>
              @if($req->prazo_devolucao->isPast())
                Vencido ({{ $req->prazo_devolucao->format('d/m/Y') }})
              @else
                {{ $req->prazo_devolucao->format('d/m/Y') }}
              @endif
            </td>

            <!-- Data de devolução -->
            <td>{{ $req->data_recepcao ? $req->data_recepcao->format('d/m/Y') : '—' }}</td>




            
            <td>
              @if($req->ativo)
                Ativo
              @else
                Finalizado
              @endif
            </td>

            <!-- Disponibilidade -->
            <td>
              @if($req->livro)
                {{ $req->livro->disponivel ? 'Disponível' : 'Indisponível' }}
              @else
                —
              @endif
            </td>

            <!-- Botão confirmar devolução -->
            <td class="@if(!auth()->check() || !auth()->user()->is_admin) hidden @endif">>
              @if($req->ativo)
              <form action="{{ route('requisicoes.confirmar', $req->id) }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-primary">Confirmar Devolução</button>
              </form>
              @endif
            </td>

            <!-- Avaliação -->
              <td class="@if(!auth()->check() || auth()->user()->is_admin) hidden @endif">
              @if(!$req->ativo && $req->livro && auth()->check() && !auth()->user()->is_admin && auth()->user()->id === $req->user_id)
                @php
                  $jaAvaliada = \App\Models\Avaliacao::where('user_id', auth()->user()->id)
                    ->where('requisicao_id', $req->id)
                    ->exists();
                @endphp
                @if($jaAvaliada)
                  <span style="background-color: #fef9c3; color: #92400e; border: 1px solid #fde68a; padding: 0.25rem 0.75rem; border-radius: 0.25rem; display: inline-block;">Avaliado</span>
                @else
                  <a href="{{ route('avaliacoes.create', ['livro_id' => $req->livro->id, 'requisicao_id' => $req->id]) }}" style="background-color: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; padding: 0.25rem 0.75rem; border-radius: 0.25rem; text-decoration: none; display: inline-block;">Avaliar</a>
                @endif
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