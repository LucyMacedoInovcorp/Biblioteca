@extends('layouts.main')
@section('title', 'Painel de Controle')
@section('content')

@if(auth()->check() && auth()->user()->is_admin)
<!-- Card: Ações de Administrador -->
<div class="col-span-1 md:col-span-2 lg:col-span-3">
    <h2 class="text-2xl font-bold text-gray-700 mb-4">Painel do Administrador</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Card: Novo Utilizador -->
        <a href="{{ route('users.create') }}" class="card bg-base-100 shadow-xl image-full hover:shadow-2xl transition-shadow duration-300">
            <figure><img src="https://placehold.co/600x400/EF4444/ffffff?text=Novo+Usuário" alt="Novo Utilizador" /></figure>
            <div class="card-body justify-end">
                <h2 class="card-title text-white">➕ Novo Utilizador</h2>
                <p class="text-gray-200 text-sm">Adicione um novo Utilizador/Administrador à biblioteca.</p>
            </div>
        </a>
        <!-- Card: Gestão do Acervo -->
        <a href="{{ route('books.search.index') }}" class="card bg-base-100 shadow-xl image-full hover:shadow-2xl transition-shadow duration-300">
            <figure><img src="https://placehold.co/600x400/3B82F6/ffffff?text=Acervo" alt="Acervo de Livros" /></figure>
            <div class="card-body justify-end">
                <h2 class="card-title text-white">📁 Gestão do Acervo</h2>
                <p class="text-gray-200 text-sm">Gerencie o catálogo de livros da biblioteca.</p>
            </div>
        </a>
        <!-- Card: Avaliações Suspensas -->
        <a href="{{ route('avaliacoes.suspensas') }}" class="card bg-base-100 shadow-xl image-full hover:shadow-2xl transition-shadow duration-300">
            <figure><img src="https://placehold.co/600x400/6366F1/ffffff?text=Suspensas" alt="Avaliações Suspensas" /></figure>
            <div class="card-body justify-end">
                <h2 class="card-title text-white">📝 Avaliações Suspensas</h2>
                <p class="text-gray-200 text-sm">Acesse e aprove ou recuse avaliações de livros enviadas pelos cidadãos.</p>
            </div>
        </a>

        <!-- Card: Todos os Pedidos -->
        <a href="{{ route('admin.encomendas.todos') }}" class="card bg-base-100 shadow-xl image-full hover:shadow-2xl transition-shadow duration-300">
            <figure><img src="https://placehold.co/600x400/10B981/ffffff?text=Pedidos" alt="Todos os Pedidos" /></figure>
            <div class="card-body justify-end">
                <h2 class="card-title text-white">📦 Todos os Pedidos</h2>
                <p class="text-gray-200 text-sm">Visualize e gerencie todos os pedidos realizados na biblioteca.</p>
            </div>
        </a>

        <!-- Card: Logs do Sistema -->
        <a href="{{ route('admin.logs.index') }}" class="card bg-base-100 shadow-xl image-full hover:shadow-2xl transition-shadow duration-300">
            <figure><img src="https://placehold.co/600x400/F59E0B/ffffff?text=Logs" alt="Logs do Sistema" /></figure>
            <div class="card-body justify-end">
                <h2 class="card-title text-white">📜 Logs do Sistema</h2>
                <p class="text-gray-200 text-sm">Monitore todas as atividades e ações realizadas no sistema.</p>
            </div>
        </a>
    </div>
</div>
@endif
</div>

</div>

@endsection