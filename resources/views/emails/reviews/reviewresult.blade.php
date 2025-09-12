<h1>📊 Resultado da Avaliação</h1>

<p>Sua Avaliação foi: <strong>{{ $status }}</strong></p>

<p>📖 <strong>Detalhes do Livro</strong></p>
<ul>
    <li><strong>Título:</strong> {{ $livro->nome }}</li>
    <li><strong>Avaliação:</strong> {{ $avaliacao->rating }}</li>
    <li><strong>Comentário:</strong> {{ $avaliacao->review }}</li>
</ul>

@if($status == 'recusada' && $justification)
    <p>Justificativa: {{ $justification }}</p>
@endif

<p>Obrigado,<br>{{ config('app.name') }}</p>
