@extends('layouts.main')

@section('title', 'Pagamento realizado')

@section('content')
<div class="max-w-lg mx-auto p-6 text-center">
    <h2 class="text-2xl font-bold mb-4 text-green-600">Pagamento efetuado com sucesso!</h2>
    <p class="mb-6">O seu pedido foi processado e o pagamento confirmado.</p>
    <a href="{{ route('encomendas.meus') }}" class="btn btn-info">Ver meus pedidos</a>
</div>
@endsection
