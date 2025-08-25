@extends('layouts.main')

@section('title', 'Editoras')

@section('content')

@if(auth()->check() && auth()->user()->is_admin)
<div id="editoras-create-container" class="max-w-2xl mx-auto mt-10">
  <div class="card bg-base-100 shadow-xl p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">🏢 Registe uma nova editora</h1>

    <form action="/editoras" method="POST" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <!-- Logotipo -->
      <div class="form-control">
        <label for="logotipo" class="label">
          <span class="label-text font-semibold">Logotipo da editora</span>
        </label>
        <input type="file" id="logotipo" name="logotipo" class="file-input file-input-bordered w-full" />
      </div>

      <!-- Nome -->
      <div class="form-control">
        <label for="nome" class="label">
          <span class="label-text font-semibold">Nome da editora</span>
        </label>
        <input type="text" id="nome" name="nome" placeholder="Digite o nome da editora"
          class="input input-bordered w-full" />
      </div>

      <!-- Botão -->
      <div class="form-control mt-6">
        <button type="submit" class="btn btn-primary w-full">➕ Incluir editora</button>
      </div>
    </form>
  </div>
</div>
@endif

<div id="editoras-container" class="w-full p-6">
  <!-- Título -->
  <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
    <h2 class="text-3xl font-bold flex items-center gap-2">
      🏢 Acervo de Editoras
      <span class="badge badge-primary badge-lg">{{ $editoras->count() ?? 0 }}</span>
    </h2>

    <!-- Filtro -->
    <label class="form-control w-64">
      <div class="label">
        <span class="label-text font-semibold">Filtrar coluna</span>
      </div>
      <select id="colSelect" class="select select-bordered">
        <option value="all">Todas</option>
      </select>
    </label>
  </div>

  <!-- Tabela -->
  <div class="card bg-base-100 shadow-md mt-10">
    <div class="card-body p-0">
      <div class="overflow-x-auto">
        <table class="myTable table table-zebra w-full">
          <thead class="bg-base-200">
            <tr>
              <th>📷 Logotipo</th>
              <th>🏢 Nome</th>
              <th scope="col" class="@if(!auth()->check() || !auth()->user()->is_admin) hidden @endif">⚙️ Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($editoras as $editora)
            <tr class="hover">
              <td>
                <div class="w-12 h-auto flex items-center justify-center bg-base-200 rounded-md shadow-sm">
                  <img src="{{ $editora->logotipo }}" alt="{{ $editora->nome }}"
                    class="max-h-full max-w-full object-contain">
                </div>


              </td>
              <td class="font-semibold text-primary">{{ $editora->nome }}</td>              
              <td class="@if(!auth()->check() || !auth()->user()->is_admin) hidden @endif flex items-center gap-3">
                <!-- Botão Editar -->
                <a href="#"
                  class="px-3 py-1 text-sm rounded-lg flex items-center gap-1 
            bg-orange-100 text-orange-700 hover:bg-orange-200 transition">
                  ✏️ Editar
                </a>
                <!-- Botão Excluir -->
                <form action="{{ route('editoras.destroy', $editora->id) }}"
                  method="POST"
                  onsubmit="return confirm('Tem certeza que deseja excluir este livro?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                    class="px-3 py-1 text-sm rounded-lg flex items-center gap-1 
                   bg-red-100 text-red-700 hover:bg-red-200 transition">
                    🗑️ Excluir
                  </button>
                </form>
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