@extends('layouts.main')

@section('title', 'Seu Carrinho')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6 text-center">🛒 Seu Carrinho</h2>

    @if($itens->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @php $total = 0; @endphp
        @foreach($itens as $item)
        @php $total += $item->preco_unitario * $item->quantidade; @endphp
        <div class="card bg-base-100 shadow-md flex flex-col items-center p-3 min-w-0">
            <img src="{{ asset($item->livro->imagemcapa ?? 'images/livro-default.png') }}"
                alt="{{ $item->livro->nome ?? $item->livro->titulo }}"
                class="w-16 h-24 object-cover rounded mb-2">
            <div class="w-full text-center">
                <div class="font-bold text-base truncate">{{ $item->livro->nome ?? $item->livro->titulo }}</div>
                <div class="text-green-700 font-semibold text-sm" data-preco-unitario="{{ $item->preco_unitario }}">
                    Preço: €{{ number_format($item->preco_unitario, 2, ',', '.') }}
                </div>
                <form action="{{ route('carrinho.atualizar', $item->id) }}" method="POST" class="flex items-center justify-center gap-2 mt-2">
                    @csrf
                    @method('PUT')
                    <span>Quantidade:</span>
                    <input type="number" name="quantidade" value="{{ $item->quantidade }}" min="1"
                        class="input input-bordered input-xs w-14 text-center" />
                    
                </form>
            </div>
            <form action="{{ route('carrinho.remover', $item->id) }}" method="POST" class="mt-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error btn-xs shadow hover:scale-105">Remover</button>
            </form>
        </div>
        @endforeach
    </div>
    <div class="mt-8 text-right">
        <span id="carrinho-total" class="text-2xl font-bold text-blue-700">
            €{{ number_format($total, 2, ',', '.') }}
        </span>
    </div>
    <div class="mt-6 text-center">
        <a href="#" class="btn btn-success btn-lg shadow-lg hover:scale-105">Finalizar Compra</a>
    </div>
    @else
    <div class="text-center text-gray-500 mt-12">
        <span class="text-2xl">Seu carrinho está vazio.</span>
    </div>
    @endif
</div>


{{-- Script para atualizar o total dinamicamente ao mudar quantidades --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('input[name="quantidade"]');
        const totalSpan = document.getElementById('carrinho-total');
        const precos = Array.from(document.querySelectorAll('[data-preco-unitario]')).map(el => parseFloat(el.dataset.precoUnitario));

        function atualizarTotal() {
            let total = 0;
            inputs.forEach((input, i) => {
                const qtd = parseInt(input.value) || 1;
                total += precos[i] * qtd;
            });
            totalSpan.textContent = '€' + total.toFixed(2).replace('.', ',');
        }

        inputs.forEach(input => {
            input.addEventListener('input', atualizarTotal);
        });
    });
</script>
@endsection