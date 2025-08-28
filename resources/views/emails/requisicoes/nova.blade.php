@component('mail::message')
# 📚 Nova Requisição de Livro
Olá {{ $requisicao->user->name }},
Sua requisição foi registrada com sucesso! ✅
---
 📖 Detalhes do Livro
- **Título:** {{ $requisicao->livro->nome }}
- **ISBN:** {{ $requisicao->livro->ISBN }}
- **Editora:** {{ $requisicao->livro->editora->nome ?? '—' }}

@if($requisicao->livro->imagemcapa)
![Capa do livro]({{ asset($requisicao->livro->imagemcapa) }})
@endif
---
 📅 Informações
- Data da requisição: {{ $requisicao->created_at->format('d/m/Y H:i') }}
- Status: **Ativo**
Obrigado,<br>
{{ config('app.name') }}
@endcomponent
Esse markdown do Laravel já formata bem os emails.