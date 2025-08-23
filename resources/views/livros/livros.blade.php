@extends('layouts.main')

@section('title', 'Livros')

@section('content')


<div id="livros-create-container" class="max-w-lg mx-auto p-6 bg-white rounded shadow mt-6">
  <h1 class="text-2xl font-bold mb-4">Registe um novo livro</h1>

  <form action="/livros" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div>
      <label for="imagemcapa" class="block text-gray-700 font-medium mb-1">Imagem do Livro:</label>
      <input type="file" id="imagemcapa" name="imagemcapa" class="block w-full text-gray-700 border border-gray-300 rounded p-2">
    </div>

    <div>
      <label for="nome" class="block text-gray-700 font-medium mb-1">Nome do Livro:</label>
      <input type="text" id="nome" name="nome" placeholder="Nome do Livro" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
      <label for="ISBN" class="block text-gray-700 font-medium mb-1">ISBN:</label>
      <input type="text" id="ISBN" name="ISBN" placeholder="ISBN" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
      <label for="bibliografia" class="block text-gray-700 font-medium mb-1">Bibliografia:</label>
      <textarea id="bibliografia" name="bibliografia" placeholder="Bibliografia" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
    </div>

    <div>
      <label for="preco" class="block text-gray-700 font-medium mb-1">Preço:</label>
      <textarea id="preco" name="preco" placeholder="Preço do livro" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
    </div>

<div class="flex justify-center">
  <input 
    type="submit" 
    value="Incluir livro" 
    class="bg-red-300 text-black font-semibold py-2 px-4 rounded border-2 border-red-400 hover:bg-red-400 hover:border-red-500 transition duration-300 cursor-pointer"
  />
</div>

    
  </form>
</div>




<div id="search-container" class="w-full p-4">
    <h1 class="text-2xl font-bold mb-2">Busque um livro</h1>
    <form action="">
        <input type="text" id="search" name="search" 
               class="w-full p-2 border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
               placeholder="Procurar...">
    </form>
</div>

<div id="livros-container" class="w-full p-4">
    <h2 class="text-xl font-semibold mb-1">Livros</h2>
    <p class="text-gray-600 mb-4">Acervo BibliON</p>


<table class="datatable min-w-full border border-gray-200 myTable">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2 border">Imagem</th> 
            <th class="px-4 py-2 border">Nome</th>
            <th class="px-4 py-2 border">ISBN</th>
            <th class="px-4 py-2 border">Bibliografia</th>
            <th class="px-4 py-2 border">Preço</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach($livros as $livro)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 border">
                <img src="{{ $livro->imagemcapa }}" alt="{{ $livro->nome }}" class="w-12 h-12 object-cover rounded">
            </td>
            <td class="px-4 py-2 border">{{ $livro->nome }}</td>
            <td class="px-4 py-2 border">{{ $livro->ISBN }}</td>
            <td class="px-4 py-2 border">{{ $livro->bibliografia }}</td>
            <td class="px-4 py-2 border">{{ $livro->preco }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


</div>






@endsection