@extends('layouts.main')

@section('title', 'Autores')

@section('content')

@if(auth()->check() && auth()->user()->is_admin)
<div id="autores-create-container" class="max-w-2xl mx-auto mt-10">
  <div class="card bg-base-100 shadow-xl p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">👤 Registe um novo autor</h1>

    <form action="/autores" method="POST" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <!-- Foto -->
      <div class="form-control">
        <label for="foto" class="label">
          <span class="label-text font-semibold">Imagem do autor</span>
        </label>
        <input type="file" id="foto" name="foto" class="file-input file-input-bordered w-full" />
      </div>

      <!-- Nome -->
      <div class="form-control">
        <label for="nome" class="label">
          <span class="label-text font-semibold">Nome do autor</span>
        </label>
        <input type="text" id="nome" name="nome" placeholder="Digite o nome do autor" 
          class="input input-bordered w-full" />
      </div>

      <!-- Botão -->
      <div class="form-control mt-6">
        <button type="submit" class="btn btn-primary w-full">➕ Incluir autor</button>
      </div>
    </form>
  </div>
</div>
@endif

<div id="autores-container" class="w-full p-6">
  <!-- Título -->
  <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
    <h2 class="text-3xl font-bold flex items-center gap-2">
      👥 Acervo de Autores
      <span class="badge badge-primary badge-lg">{{ $autores->count() ?? 0 }}</span>
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
              <th>📷 Imagem</th>
              <th>👤 Nome</th>
              <th scope="col" class="@if(!auth()->check() || !auth()->user()->is_admin) invisible @endif">⚙️ Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($autores as $autor)
              <tr class="hover">
                <td>
                  <img src="{{ asset($autor->foto) }}" alt="{{ $autor->nome }}" 
                       class="w-12 h-12 object-cover rounded-md shadow-sm">
                </td>
                <td class="font-semibold text-primary">{{ $autor->nome }}</td>                              
              <td class="@if(!auth()->check() || !auth()->user()->is_admin) invisible @endif flex items-center gap-3">

                    <!-- Botão Editar -->
                    <a href="{{ route('autores.edit', $autor->id) }}"
                      class="px-3 py-1 text-sm rounded-lg flex items-center gap-1 
                bg-orange-100 text-orange-700 hover:bg-orange-200 transition">
                      ✏️ Editar
                    </a>
                <!-- Botão Excluir -->
                <form action="{{ route('autores.destroy', $autor->id) }}"
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
