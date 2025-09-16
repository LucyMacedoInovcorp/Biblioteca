@extends('layouts.main')

@section('title', 'Pagamento não concluído')

@section('content')
<div class="max-w-lg mx-auto p-6 text-center">
    <h2 class="text-2xl font-bold mb-4 text-red-600">Pagamento não realizado</h2>
    <p class="mb-6">O pagamento foi cancelado ou ocorreu um erro durante o processo.</p>
    <a href="{{ route('checkout.form') }}" class="btn btn-primary">Tentar novamente</a>
</div>
@endsection
