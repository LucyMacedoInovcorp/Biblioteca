@extends('layouts.main')
@section('title', 'Livros')
@section('content')


<div id="livros-edit-container" class="max-w-2xl mx-auto mt-10">
  <div class="card bg-base-100 shadow-xl p-8">
    <h1 class="text-3xl font-bold mb-6 text-center">✏️ Editar o livro:  <span class="text-blue-600">{{ $livro->nome }}</span></h1>
<form action="{{ route('livros.update', $livro->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method('PUT')
        <!-- Imagem da Capa -->
    <div class="form-control">
        <label for="imagemcapa" class="label">
            <span class="label-text font-semibold">Imagem da Capa</span>
        </label>

        <!-- Preview da capa atual -->
        @if($livro->imagemcapa)
            <div class="mb-3">
                <p class="text-sm text-gray-500">📌 Capa atual:</p>
                <img src="{{ asset($livro->imagemcapa) }}" alt="Capa do livro"
                     class="w-32 h-44 object-cover rounded-lg shadow-md">
            </div>
        @endif

        <!-- Input para nova imagem -->
        <input type="file" id="imagemcapa" name="imagemcapa"
               class="file-input file-input-bordered w-full"
               onchange="previewImage(event)" />

        <!-- Preview da nova imagem escolhida -->
        <div class="mt-3 hidden" id="preview-container">
            <p class="text-sm text-gray-500">📷 Nova imagem selecionada:</p>
            <img id="preview" class="w-32 h-44 object-cover rounded-lg shadow-md">
        </div>
    </div>


    <!-- Nome -->
    <div class="form-control">
        <input type="text" id="nome" name="nome" value="{{ old('nome', $livro->nome) }}"
            class="input input-bordered w-full" />
    </div>

    <!-- ISBN -->
    <div class="form-control">
        <label for="ISBN" class="label">
            <span class="label-text font-semibold">ISBN</span>
        </label>
        <input type="text" id="ISBN" name="ISBN" value="{{ old('ISBN', $livro->ISBN) }}"
            class="input input-bordered w-full" />
    </div>

    <!-- Bibliografia -->
<div class="form-control">
    <label for="bibliografia" class="label">
        <span class="label-text font-semibold"> Bibliografia</span>
    </label>
    <textarea id="bibliografia" name="bibliografia" rows="4"
        class="textarea textarea-bordered w-full"
        placeholder="Insira a bibliografia do livro">{{ old('bibliografia', $livro->bibliografia) }}</textarea>
</div>


    <div class="form-control">
        <label for="preco" class="label">
            <span class="label-text font-semibold">Preço</span>
        </label>
        <input type="number" step="0.01" id="preco" name="preco"
            value="{{ old('preco', $livro->preco) }}"
            placeholder="Preço do livro (€)"
            class="input input-bordered w-full" required />
    </div>


    <!-- Editora -->
    <div class="form-control">
        <label for="editora_id" class="label">Editora</label>
        <select id="editora_id" name="editora_id" class="select select-bordered w-full" required>
            @foreach($editoras as $editora)
            <option value="{{ $editora->id }}" {{ $editora->id == $livro->editora_id ? 'selected' : '' }}>
                {{ $editora->nome }}
            </option>
            @endforeach
        </select>
    </div>

    <!-- Autores -->
    <div class="form-control">
        <label for="autores" class="label">Autores</label>
        <select id="autores" name="autores[]" class="select select-bordered w-full" multiple required>
            @foreach($autores as $autor)
            <option value="{{ $autor->id }}" {{ $livro->autores->contains($autor->id) ? 'selected' : '' }}>
                {{ $autor->nome }}
            </option>
            @endforeach
        </select>
    </div>

    <!-- Botão -->
    <div class="form-control mt-6">
        <button type="submit" class="btn btn-success w-full">💾 Atualizar livro</button>
    </div>
</form>

{{--previewImage--}}
<script>
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const container = document.getElementById('preview-container');
        preview.src = URL.createObjectURL(event.target.files[0]);
        container.classList.remove('hidden');
    }
</script>


@endsection