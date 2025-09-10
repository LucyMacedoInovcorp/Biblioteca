@component('mail::message')
# 🟢 Novo Livro disponível


📖 O livro "{{ $livro->nome }}" já está disponível! ✅

---

@if(isset($imagemPath))
    <img src="{{ $imagemPath }}" alt="Capa" style="max-width:150px;">
@endif

---

Faça já sua requisição!<br>
{{ config('app.name') }}
@endcomponent
