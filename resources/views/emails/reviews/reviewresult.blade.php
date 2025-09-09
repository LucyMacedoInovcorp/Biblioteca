<h1>📊 Resultado da Avaliação</h1>

<p>Sua review foi: <strong>{{ $status }}</strong></p>

@if($status == 'recusada' && $justification)
    <p>Justificativa: {{ $justification }}</p>
@endif

<p>Obrigado,<br>{{ config('app.name') }}</p>
