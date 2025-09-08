@extends('layouts.main')
@section('title', 'Avaliações Suspensas')
@section('content')
<div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">📝 Avaliações Suspensas</h2>
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
{{-- Arquivo movido para suspensas.blade.php --}}
{{-- A tabela e o conteúdo foram removidos para evitar duplicidade --}}
</div>
@endsection
