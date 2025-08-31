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
<img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path($requisicao->livro->imagemcapa))) }}" alt="Capa">
@endif


---
 📅 Informações
- Data da requisição: {{ $requisicao->created_at->format('d/m/Y H:i') }}
- Status: **Ativo**<br>
Obrigado,<br>
{{ config('app.name') }}
@endcomponent
