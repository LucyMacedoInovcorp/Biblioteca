@extends('layouts.main')
@section('title', 'Meus Pedidos')
@section('content')

@if(session('success'))
<div class="mb-4" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.75rem 1rem; border-radius: 0.375rem;">
    {{ session('success') }}
</div>
@endif


<div class="w-full p-6">
    <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
        <h2 class="text-3xl font-bold flex items-center gap-2">
            📦 Meus Pedidos
            <span class="badge badge-primary badge-lg">{{ $encomendas->count() }}</span>
        </h2>
    </div>
    <div class="card bg-base-100 shadow-md mt-10">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="myTable table table-zebra w-full">
                    <thead class="bg-base-200">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Data</th>
                            <th scope="col">Status</th>
                            <th scope="col">Total</th>
                            <!-- <th scope="col">Itens</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($encomendas as $encomenda)
                        <tr class="hover">
                            <td class="font-semibold">{{ $encomenda->id }}</td>
                            <td>{{ $encomenda->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ ucfirst($encomenda->status) }}</td>
                            <td class="font-medium text-success whitespace-nowrap">€{{ number_format($encomenda->total, 2, ',', '.') }}</td>
                            <!-- <td>
                                <ul class="list-disc ml-4">
                                    @foreach($encomenda->itens as $item)
                                        <li>{{ $item->livro->titulo ?? 'Livro removido' }} ({{ $item->quantidade }})</li>
                                    @endforeach
                                </ul>
                            </td> -->
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8">Você ainda não fez nenhum pedido.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
