@extends('layouts.main')
@section('title', 'Autores')
@section('content')

<div id="autores-edit-container" class="max-w-2xl mx-auto mt-10">
    <div class="card bg-base-100 shadow-xl p-8">
        <h1 class="text-3xl font-bold mb-6 text-center">
            ✏️ Editar o autor: <span class="text-blue-600">{{ $autor->nome }}</span>
        </h1>

        <form action="{{ route('autores.update', $autor->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Foto do autor -->
            <div class="form-control">
                @if($autor->foto)
                    <div class="mb-3">
                        <p class="text-sm text-gray-500">📌 Foto atual:</p>
                        <img src="{{ asset($autor->foto) }}" alt="Foto do autor"
                             class="w-32 h-44 object-cover rounded-lg shadow-md">
                    </div>
                @endif

                <!-- Input para nova foto -->
                <input type="file" id="foto" name="foto"
                       class="file-input file-input-bordered w-full"
                       onchange="previewImage(event)" />

                <!-- Preview da nova foto escolhida -->
                <div class="mt-3 hidden" id="preview-container">
                    <p class="text-sm text-gray-500">📷 Nova foto selecionada:</p>
                    <img id="preview" class="w-32 h-44 object-cover rounded-lg shadow-md">
                </div>
            </div>

            <!-- Nome -->
            <div class="form-control">
                <input type="text" id="nome" name="nome"
                       value="{{ old('nome', $autor->nome) }}"
                       class="input input-bordered w-full" />
            </div>

            <!-- Botão -->
            <div class="form-control mt-6">
                <button type="submit" class="btn btn-success w-full">💾 Atualizar autor</button>
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
