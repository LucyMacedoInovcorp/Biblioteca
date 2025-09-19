<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrinho;
use App\Models\ItemCarrinho;
use App\Models\Livro;
use Illuminate\Support\Facades\Auth;


class CarrinhoController extends Controller
{
    // Adiciona um livro ao carrinho do usuário
    public function adicionar(Request $request, Livro $livro) {
        $user = Auth::user();
        $carrinho = $user->carrinho ?? Carrinho::create(['user_id' => $user->id]);
        $item = $carrinho->itens()->where('livro_id', $livro->id)->first();

        if ($item) {
            $item->quantidade += 1;
            $item->save();
        } else {
            $novoItem = $carrinho->itens()->create([
                'livro_id' => $livro->id,
                'quantidade' => 1,
                'preco_unitario' => $livro->preco
            ]);
        }

        // Dispara o job para enviar e-mail de carrinho abandonado após 1 hora
        //\App\Jobs\EnviarCarrinhoAbandonadoJob::dispatch($carrinho)->delay(now()->addHour());

        \App\Jobs\EnviarCarrinhoAbandonadoJob::dispatch($carrinho)->delay(now()->addMinute());

        return redirect()->route('carrinho.listar')->with('success', 'Livro adicionado ao carrinho!');
    }



// Lista os itens no carrinho do usuário
public function listar() {
    $user = Auth::user();
    $carrinho = $user->carrinho;
    $itens = $carrinho ? $carrinho->itens()->with('livro')->get() : collect();

    return view('carrinho.listar', compact('itens'));
}

// Remove um item do carrinho
public function remover(ItemCarrinho $item) {
    $item->delete();
    return back()->with('success', 'Item removido do carrinho!');
}


// Atualiza a quantidade de um item no carrinho
public function atualizar(Request $request, ItemCarrinho $item) {
    $item->quantidade = max(1, (int)$request->quantidade);
    $item->save();
    return back()->with('success', 'Quantidade atualizada!');
}
}
