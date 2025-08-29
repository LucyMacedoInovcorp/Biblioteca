@component('mail::message')
# Olá {{ $requisicao->user->name }},

Este é um lembrete de que o livro **{{ $requisicao->livro->nome }}** deve ser devolvido até **{{ $requisicao->data_recepcao->format('d/m/Y') }}**.

@component('mail::panel')
📖 {{ $requisicao->livro->nome }} <br>
Autor: {{ $requisicao->livro->autor ?? '—' }}
@endcomponent

Por favor, não se esqueça da devolução.

Obrigado,<br>
Biblioteca App
@endcomponent
