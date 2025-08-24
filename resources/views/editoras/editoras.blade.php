@extends('layouts.main')

@section('title', 'Editoras')

@section('content')

{{--
<div id="editoras-create-container" class="max-w-lg mx-auto p-6 bg-white rounded shadow mt-6">
  <h1 class="text-2xl font-bold mb-4">Registe uma nova editora</h1>

  <form action="/editoras" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div>
      <label for="logotipo" class="block text-gray-700 font-medium mb-1">Logotipo da editora:</label>
      <input type="file" id="logotipo" name="logotipo" class="block w-full text-gray-700 border border-gray-300 rounded p-2">
    </div>

    <div>
      <label for="nome" class="block text-gray-700 font-medium mb-1">Nome da editora:</label>
      <input type="text" id="nome" name="nome" placeholder="Nome da editora" class="block w-full border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>


<div class="flex justify-center">
  <input 
    type="submit" 
    value="Incluir editora" 
    class="bg-red-300 text-black font-semibold py-2 px-4 rounded border-2 border-red-400 hover:bg-red-400 hover:border-red-500 transition duration-300 cursor-pointer"
  />
</div>

    
  </form>
</div>

--}}

  <div class="mb-4">
    <label for="colSelect" class="mr-2 font-medium">Filtrar coluna:</label>
    <select id="colSelect" class="border p-2 rounded">
      <option value="all">Todas</option>
    </select>
  </div>

<div id="editoras-container" class="w-full p-4">
    <h2 class="text-xl font-semibold mb-1">Editoras</h2>
    <p class="text-gray-600 mb-4">Acervo BibliON</p>

<table class="min-w-full border border-gray-200 myTable">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2 border">Logotipo</th> 
            <th class="px-4 py-2 border">Editora</th>

        </tr>
    </thead>
    <tbody>
        @foreach($editoras as $editora)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2 border">
                <img src="{{ $editora->logotipo }}" alt="{{ $editora->nome }}" class="w-12 h-12 object-cover rounded">
            </td>
            <td class="px-4 py-2 border">{{ $editora->nome }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>

@endsection