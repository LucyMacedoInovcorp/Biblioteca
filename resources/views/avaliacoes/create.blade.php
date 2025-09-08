@extends('layouts.main')
@section('title', 'Avaliação')
@section('content')


<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">✍️ Avaliação do Livro</h2>
    <form action="{{ route('avaliacoes.store') }}" method="POST" class="space-y-6">
        @csrf
        <!-- Bloco de texto para avaliação -->
        <div class="form-control">
            <label for="review" class="label font-semibold">Sua avaliação</label>
            <textarea id="review" name="review" rows="4" class="textarea textarea-bordered w-full" placeholder="Digite sua avaliação..."></textarea>
        </div>
        <!-- Range de nota -->
        <div class="form-control">
            <label for="rating" class="label font-semibold mb-2 block text-center">Nota (1 a 10)</label>
            <div class="w-full flex flex-col items-center">
            <input type="range" min="1" max="10" value="5" id="rating" name="rating" class="range range-primary w-full" />
            <div class="flex justify-between text-xs px-2 mt-1 w-full">
                @for ($i = 1; $i <= 10; $i++)
                <span class="w-6 text-center">{{ $i }}</span>
                @endfor
            </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-full">Enviar Avaliação</button>
    </form>
</div>
@endsection
