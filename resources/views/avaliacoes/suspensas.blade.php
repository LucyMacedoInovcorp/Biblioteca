@extends('layouts.main')
@section('title', 'Avaliações Suspensas')
@section('content')
<div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Avaliações Suspensas</h2>
    @if(session('success'))
        <div class="mb-4" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.75rem 1rem; border-radius: 0.375rem;">
            {{ session('success') }}
        </div>
    @endif
    <table class="table w-full">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Livro</th>
                <th>Nota</th>
                <th>Avaliação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($avaliacoes as $avaliacao)
            <tr>
                <td>{{ $avaliacao->user->name ?? '-' }}</td>
                <td>{{ $avaliacao->livro->nome ?? '-' }}</td>
                <td>{{ $avaliacao->rating }}</td>
                <td>{{ $avaliacao->review }}</td>
                <td class="flex gap-2">
                    <form action="{{ route('avaliacoes.suspensas.aprovar', $avaliacao->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Ativar</button>
                    </form>
                    <form action="{{ route('avaliacoes.suspensas.recusar', $avaliacao->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-error btn-sm">Recusar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Nenhuma avaliação suspensa.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
