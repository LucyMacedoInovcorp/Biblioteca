@extends('layouts.main')

@section('title', 'Finalizar Pedido')

@section('content')



<div class="max-w-lg mx-auto p-6">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <h2 class="text-2xl font-bold mb-4">Morada de Entrega</h2>


    <form action="{{ route('checkout.finalizar') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="logradouro" class="block font-semibold mb-1">Rua</label>
            <input type="text" name="logradouro" id="logradouro" class="input input-bordered w-full" required>

            <label for="numero" class="block font-semibold mb-1">Número</label>
            <input type="text" name="numero" id="numero" class="input input-bordered w-full" required>

            <label for="porta" class="block font-semibold mb-1">Porta</label>
            <input type="text" name="porta" id="porta" class="input input-bordered w-full">

            <label for="localidade" class="block font-semibold mb-1">Localidade</label>
            <input type="text" name="localidade" id="localidade" class="input input-bordered w-full" required>

            <label for="concelho" class="block font-semibold mb-1">Concelho</label>
            <input type="text" name="concelho" id="concelho" class="input input-bordered w-full" required>

            <label for="codigo_postal" class="block font-semibold mb-1">Código Postal</label>
            <input
                type="text"
                name="codigo_postal"
                id="codigo_postal"
                class="input input-bordered w-full"
                required
                pattern="\d{4}-\d{3}"
                title="Formato esperado: 1234-567">

            <label for="pais" class="block font-semibold mb-1">País</label>
            <input type="text" name="pais" id="pais" class="input input-bordered w-full" required>
        </div>
        <!-- Adicione outros campos se necessário -->
        <button type="submit" class="btn btn-success w-full">Efetuar pagamento</button>
    </form>
</div>


@endsection