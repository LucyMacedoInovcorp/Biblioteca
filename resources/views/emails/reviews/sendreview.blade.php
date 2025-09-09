@component('mail::message')
# ✍️ Nova Avaliação


Foi recebida uma nova avaliação! ✅

---

📖 **Detalhes do Livro**
- **Título:** {{ $livro->nome }}
- **Cidadão:** {{ $avaliacao->user->name }}
- **Avaliação:** {{ $avaliacao->rating }} 
- **Comentário:** {{ $avaliacao->review }}


@if(isset($imagemPath))
    <img src="{{ $imagemPath }}" alt="Capa" style="max-width:150px;">
@endif

---

Aguardamos a análise da avaliação!<br>
{{ config('app.name') }}
@endcomponent
