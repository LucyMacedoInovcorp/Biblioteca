@extends('layouts.main')
@section('title', 'Buscar Livros')
@section('content')

<!-- Formulário de Pesquisa -->
<!-- Formulário de Pesquisa com botão embutido -->
<form action="{{ route('books.search.results') }}" method="GET" class="mb-8 w-full max-w-md mx-auto">
    <div class="flex">
        <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Título, autor, ISBN..."
               class="input input-bordered flex-grow rounded-r-none" required>
        <button type="submit" class="btn btn-primary rounded-l-none">Pesquisar</button>
    </div>
</form>


<!-- Mensagens de Feedback -->
@if(session('msg'))
<div class="alert alert-success shadow-lg mb-4">
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('msg') }}</span>
    </div>
</div>
@endif

<!-- Resultados da Pesquisa -->
@if (isset($results) && isset($results['items']))
<h3 class="text-xl font-semibold mb-4 text-gray-700">Resultados para "{{ $query }}"</h3>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach ($results['items'] as $item)
        @php
            $volumeInfo = $item['volumeInfo'] ?? [];
            $title = $volumeInfo['title'] ?? 'N/A';
            $authors = $volumeInfo['authors'] ?? [];
            $publisher = $volumeInfo['publisher'] ?? 'N/A';
            $description = $volumeInfo['description'] ?? 'N/A';
            $thumbnail = $volumeInfo['imageLinks']['thumbnail'] ?? 'https://placehold.co/128x192/E5E7EB/4B5563?text=Sem+Capa';
            $isbn = '';
            if (isset($volumeInfo['industryIdentifiers'])) {
                foreach ($volumeInfo['industryIdentifiers'] as $identifier) {
                    if ($identifier['type'] === 'ISBN_13') {
                        $isbn = $identifier['identifier'];
                        break;
                    }
                }
            }
        @endphp

        <div class="card bg-base-100 shadow-lg hover:shadow-xl transition rounded-lg overflow-hidden">
            <div class="flex p-4 space-x-4">
                <!-- Capa -->
                <div class="w-28 flex-shrink-0">
                    <img src="{{ $thumbnail }}" alt="{{ $title }}" class="rounded-md shadow-md">
                </div>

                <!-- Informações -->
                <div class="flex flex-col justify-between flex-grow">
                    <div>
                        <h2 class="card-title text-lg font-bold text-gray-800 line-clamp-2">{{ $title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="font-semibold">Autor(es):</span> {{ implode(', ', $authors) ?: 'N/A' }}<br>
                            <span class="font-semibold">Editora:</span> {{ $publisher }}<br>
                            <span class="font-semibold">ISBN:</span> {{ $isbn ?: 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-2 line-clamp-3">{{ $description }}</p>
                    </div>

                    <!-- Botão salvar -->
                    <div class="mt-3">
                        <form action="{{ route('books.storeFromApi') }}" method="POST">
                            @csrf
                            <input type="hidden" name="title" value="{{ $title }}">
                            <input type="hidden" name="isbn" value="{{ $isbn }}">
                            @foreach ($authors as $author)
                                <input type="hidden" name="authors[]" value="{{ $author }}">
                            @endforeach
                            <input type="hidden" name="publisher" value="{{ $publisher }}">
                            <input type="hidden" name="description" value="{{ $description }}">
                            <input type="hidden" name="cover_image" value="{{ $thumbnail }}">
                            <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endforeach
</div>
@elseif (isset($query))
<p class="text-center text-gray-500">Nenhum livro encontrado para "{{ $query }}". Tente outra pesquisa.</p>
@endif

@endsection
