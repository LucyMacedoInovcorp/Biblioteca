@extends('layouts.main')
@section('title', 'Editoras')
@section('content')

<div id="editoras-edit-container" class="max-w-2xl mx-auto mt-10">
    <div class="card bg-base-100 shadow-xl p-8">
        <h1 class="text-3xl font-bold mb-6 text-center">
            ✏️ Editar a Editora: <span class="text-blue-600">{{ $editora->nome }}</span>
        </h1>

        <form action="{{ route('editoras.update', $editora->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Logotipo da Editora -->
            <div class="form-control">
                @if($editora->logotipo)
                    <div class="mb-3">
                        <p class="text-sm text-gray-500">📌 Logotipo atual:</p>
                        <img src="{{ asset($editora->logotipo) }}" alt="Logotipo da Editora"
                             class="w-32 h-44 object-cover rounded-lg shadow-md">
                    </div>
                @endif

                <!-- Input para novo logotipo -->
                <input type="file" id="logotipo" name="logotipo"
                       class="file-input file-input-bordered w-full"
                       onchange="previewImage(event)" />

                <!-- Preview do novo logotipo escolhido -->
                <div class="mt-3 hidden" id="preview-container">
                    <p class="text-sm text-gray-500">📷 Novo logotipo selecionado:</p>
                    <img id="preview" class="w-32 h-44 object-cover rounded-lg shadow-md">
                </div>
            </div>

            <!-- Nome -->
            <div class="form-control">
                <input type="text" id="nome" name="nome"
                       value="{{ old('nome', $editora->nome) }}"
                       class="input input-bordered w-full" />
            </div>

            <!-- Botão -->
            <div class="form-control mt-6">
                <button type="submit" class="btn btn-success w-full">💾 Atualizar Editora</button>
            </div>
        </form>

        {{-- previewImage --}}
        <script>
            function previewImage(event) {
                const preview = document.getElementById('preview');
                const container = document.getElementById('preview-container');
                preview.src = URL.createObjectURL(event.target.files[0]);
                container.classList.remove('hidden');
            }
        </script>
    </div>
</div>

@endsection
