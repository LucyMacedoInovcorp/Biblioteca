@extends('layouts.main')
@section('title', 'Pesquisa de Livros')
@section('content')

<h1 class="text-3xl font-bold mb-6">🔍 Resultados para "{{ $query }}"</h1>

@if($livros->isNotEmpty())
    <div>
        <h2 class="text-2xl font-semibold mb-4">📖 Acervo Local</h2>
        @include('livros.partials.livros_table', ['livros' => $livros])
    </div>
@endif


@if(!empty($googleBooks))
    <div class="mt-10">
        <h2 class="text-2xl font-bold mb-4">💡 Sugeridos pelo Google Books</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($googleBooks as $book)
                @php
                    $info = $book['volumeInfo'] ?? [];
                @endphp
                <div class="card bg-base-100 shadow-md p-4">
                    <img src="{{ $info['imageLinks']['thumbnail'] ?? 'https://via.placeholder.com/100x150' }}"
                         alt="{{ $info['title'] ?? 'Sem título' }}"
                         class="w-24 h-36 object-cover mx-auto mb-3 rounded">
                    <h3 class="text-lg font-semibold">{{ $info['title'] ?? 'Sem título' }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ implode(', ', $info['authors'] ?? ['Autor desconhecido']) }}
                    </p>
                    @auth
                        <form action="{{ route('wishlist.store') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="google_book_id" value="{{ $book['id'] }}">
                            <input type="hidden" name="isbn" value="{{ $info['industryIdentifiers'][0]['identifier'] ?? '' }}">
                            <input type="hidden" name="title" value="{{ $info['title'] ?? '' }}">
                            <input type="hidden" name="authors" value="{{ implode(', ', $info['authors'] ?? []) }}">
                            <input type="hidden" name="cover_url" value="{{ $info['imageLinks']['thumbnail'] ?? '' }}">
                            <input type="hidden" name="raw_json" value="{{ json_encode($book) }}">
                            <button type="submit" class="btn btn-outline btn-primary w-full">
                                💡 Sugerir para a Biblioteca
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline btn-primary w-full">
                            🔑 Faça login para sugerir
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
