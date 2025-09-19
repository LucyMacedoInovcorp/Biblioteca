<div style="font-family: Arial, sans-serif; background: #f9fafb; padding: 32px;">
    <div style="max-width: 600px; margin: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 32px;">
        <h2 style="color: #1a202c;">🛒 Olá {{ $user->name }},</h2>
        <p style="font-size: 16px; color: #333;">Notamos que você adicionou livros ao seu carrinho, mas ainda não finalizou a solicitação.</p>

        @if(isset($imagemPath))
        <img src="{{ $imagemPath }}" alt="Capa" style="max-width:150px;">
        @endif
        @if($livro)
        <div style="text-align:center; margin-bottom:24px;">

            <p style="margin-top:8px; font-size:17px; color:#1a202c;"><strong>{{ $livro->nome }}</strong></p>
        </div>
        @endif

        <div style="text-align: center; margin: 24px 0;">
            <a href="{{ url('/carrinho') }}" style="display: inline-block; color: #2563eb; text-decoration: underline; font-weight: bold; padding: 12px 28px; border-radius: 5px; font-size: 17px; border: 1px solid #2563eb; background: none;">Acessar meu carrinho</a>
        </div>

        <p style="font-size: 15px; color: #333;">Contate-nos se precisar de ajuda para finalizar seu pedido.</p>

        <p style="margin-top: 32px; color: #1a202c;">Obrigado,<br><strong>Biblioteca App</strong></p>
    </div>
</div>