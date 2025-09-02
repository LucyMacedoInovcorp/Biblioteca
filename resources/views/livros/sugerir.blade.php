@extends('layouts.main')
@section('title', 'Sugerir Livros')
@section('content')

<!-- Formulário de Pesquisa -->

<form action="{{ route('livros.sugestoes.index') }}" method="GET" class="mb-8 w-full max-w-md mx-auto">
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

<!-- Exibir Resultados da Pesquisa ou a Tabela de Sugestões -->

@if (isset($results) && isset($results['items']))
<!-- Resultados da Pesquisa -->
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
                    <div class="mt-3">
                        <form action="{{ route('sugestoes.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="titulo" value="{{ $title }}">
                            <input type="hidden" name="autor" value="{{ implode(', ', $authors) }}">
                            <input type="hidden" name="isbn" value="{{ $isbn }}">
                            <input type="hidden" name="editora" value="{{ $publisher }}">
                            <input type="hidden" name="descricao" value="{{ $description }}">
                            <input type="hidden" name="imagem_capa" value="{{ $thumbnail }}">
                            <button type="submit" class="btn btn-sm btn-primary">Sugerir Aquisição</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@elseif (isset($query))
<p class="text-center text-gray-500">Nenhum livro encontrado para "{{ $query }}". Tente outra pesquisa.</p>
@else
<!-- Tabela de Sugestões Pendentes -->
<h2 class="text-2xl font-bold mb-6">Sugestões de Livros</h2>
<div class="overflow-x-auto">
<table class="table w-full bg-base-100 shadow-xl rounded-lg">
<thead>
<tr>
<th>Título</th>
<th>Autor</th>
<th>ISBN</th>
<th>Sugestão por</th>
<th>Status</th>
<th>Ações</th>
</tr>
</thead>
<tbody>
@foreach ($sugestoes as $sugestao)
<tr>
<td>{{ $sugestao->titulo }}</td>
<td>{{ $sugestao->autor }}</td>
<td>{{ $sugestao->isbn }}</td>
<td>{{ $sugestao->user->name }}</td>
<td>
<span class="badge badge-warning">{{ $sugestao->status }}</span>
</td>
<td class="flex space-x-2">
@if (auth()->user()->is_admin)
<form action="{{ route('sugestoes.aprovar', $sugestao->id) }}" method="POST">
@csrf
<button type="submit" class="btn btn-sm btn-success">Aprovar</button>
</form>
<form action="{{ route('sugestoes.destroy', $sugestao->id) }}" method="POST">
@csrf
@method('DELETE')
<button type="submit" class="btn btn-sm btn-error">Rejeitar</button>
</form>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif

@endsection