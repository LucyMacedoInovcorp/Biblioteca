@extends('layouts.main')
@section('title', 'Livros')
@section('content')

@if(auth()->check() && auth()->user()->is_admin)
<div id="livros-create-container" class="max-w-2xl mx-auto mt-10">
  <div class="card bg-base-100 shadow-xl p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">📚 Registe um novo livro</h1>
    <form action="/livros" method="POST" enctype="multipart/form-data" class="space-y-5">
      @csrf
      <!-- Capa -->
      <div class="form-control">
        <label for="imagemcapa" class="label">
          <span class="label-text font-semibold">Imagem do Livro</span>
        </label>
        <input type="file" id="imagemcapa" name="imagemcapa" class="file-input file-input-bordered w-full" />
      </div>
      <!-- Nome -->
      <div class="form-control">
        <label for="nome" class="label">
          <span class="label-text font-semibold">Nome do Livro</span>
        </label>
        <input type="text" id="nome" name="nome" placeholder="Digite o nome do livro"
          class="input input-bordered w-full" />
      </div>
      <!-- ISBN -->
      <div class="form-control">
        <label for="ISBN" class="label">
          <span class="label-text font-semibold">ISBN</span>
        </label>
        <input type="text" id="ISBN" name="ISBN" placeholder="Digite o ISBN"
          class="input input-bordered w-full" />
      </div>
      <!-- Bibliografia -->
      <div class="form-control">
        <label for="bibliografia" class="label">
          <span class="label-text font-semibold">Bibliografia</span>
        </label>
        <textarea id="bibliografia" name="bibliografia" placeholder="Escreva a bibliografia"
          class="textarea textarea-bordered w-full"></textarea>
      </div>
      <!-- Preço -->
      <div class="form-control">
        <label for="preco" class="label">
          <span class="label-text font-semibold">Preço</span>
        </label>
        <input type="number" step="0.01" id="preco" name="preco" placeholder="Preço do livro (€)"
          class="input input-bordered w-full" />
      </div>
      <!-- Editora -->
      <div class="form-control">
        <label for="editora_id" class="label">
          <span class="label-text font-semibold">Editora</span>
        </label>
        <select id="editora_id" name="editora_id" class="select select-bordered w-full" required>
          <option disabled selected>Selecione a editora</option>
          @foreach($editoras as $editora)
          <option value="{{ $editora->id }}">{{ $editora->nome }}</option>
          @endforeach
        </select>
      </div>
      <!-- Autores -->
      <div class="form-control">
        <label for="autores" class="label">
          <span class="label-text font-semibold">Autores</span>
        </label>
        <select id="autores" name="autores[]" class="select select-bordered w-full" multiple required>
          @foreach($autores as $autor)
          <option value="{{ $autor->id }}">{{ $autor->nome }}</option>
          @endforeach
        </select>
        <small class="text-gray-500 mt-1">💡 Segure CTRL (ou CMD no Mac) para selecionar vários autores</small>
      </div>
      <!-- Botão -->
      <div class="form-control mt-6">
        <button type="submit" class="btn btn-primary w-full">➕ Incluir livro</button>
      </div>
    </form>
  </div>
</div>
@endif
<div id="livros-container" class="w-full p-6">
  <!-- Título -->
  <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
    <h2 class="text-3xl font-bold flex items-center gap-2">
      📖 Acervo de Livros
      <span class="badge badge-primary badge-lg">{{ $livros->count() ?? 0 }}</span>
    </h2>
    <!-- Filtro de colunas -->
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
              <th scope="col">📷 Imagem</th>
              <th scope="col">📚 Nome</th>
              <th scope="col">🔢 ISBN</th>
              <th scope="col">📝 Bibliografia</th>
              <th scope="col">💶 Preço</th>
              <th scope="col">🏢 Editora</th>
              <th scope="col">👤 Autores</th>
              <th scope="col" class="@if(!auth()->check() || !auth()->user()->is_admin) hidden @endif">⚙️ Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($livros as $livro)
            <tr class="hover">
              <td>
                <img src="{{ $livro->imagemcapa }}" alt="{{ $livro->nome }}"
                  class="w-12 h-12 object-cover rounded-md shadow-sm">
              </td>
              <td class="font-semibold text-primary">
                {{ $livro->nome }}
              </td>
              <td class="whitespace-nowrap">{{ $livro->ISBN }}</td>
              <td class="max-w-xs truncate" title="{{ $livro->bibliografia }}">
                {{ $livro->bibliografia }}
              </td>
              <td class="font-medium text-success whitespace-nowrap">
                {{ number_format($livro->preco, 2, ',', '.') }} €
              </td>
              <td>{{ $livro->editora->nome ?? '—' }}</td>
              <td>
                @if($livro->autores->isNotEmpty())
                <div class="flex flex-wrap gap-1">
                  @foreach($livro->autores as $autor)
                  <span class="badge badge-outline badge-primary">{{ $autor->nome }}</span>
                  @endforeach
                </div>
                @else
                <span class="badge badge-ghost">Sem autor</span>
                @endif
              </td>
              <td class="@if(!auth()->check() || !auth()->user()->is_admin) hidden @endif flex items-center gap-3">

                <!-- Botão Editar -->
                <a href="#"
                  class="px-5 py-5 text-sm rounded-lg flex items-center gap-1 
            bg-orange-100 text-orange-700 hover:bg-orange-200 transition">
                  ✏️ Editar
                </a>
                <!-- Botão Excluir -->
                <form action="{{ route('livros.destroy', $livro->id) }}"
                  method="POST"
                  onsubmit="return confirm('Tem certeza que deseja excluir este livro?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                    class="px-5 py-5 text-sm rounded-lg flex items-center gap-1 
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
