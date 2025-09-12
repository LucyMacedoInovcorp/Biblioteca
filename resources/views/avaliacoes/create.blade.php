@extends('layouts.main')
@section('title', 'Avaliação')
@section('content')

@if(session('success'))
<div class="mb-4" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.75rem 1rem; border-radius: 0.375rem;">
    {{ session('success') }}
</div>
@endif

<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">✍️ Avaliação do Livro</h2>

    @if($errors->any())
    <div class="alert alert-error mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('avaliacoes.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="livro_id" value="{{ $livro_id }}">
        <input type="hidden" name="requisicao_id" value="{{ $requisicao_id }}">
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