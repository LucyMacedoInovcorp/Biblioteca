<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;


class CheckoutController extends Controller
{

    public function form()
    {
        $user = Auth::user();
        $carrinho = $user->carrinho;
        $itens = $carrinho ? $carrinho->itens()->with('livro')->get() : collect();
        return view('checkout.form', compact('itens'));
    }

    

    public function finalizar(Request $request)
    {

        
        $user = Auth::user();

        // Validação
        $request->validate([
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'porta' => 'nullable|string|max:10',
            'localidade' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:12',
            'concelho' => 'required|string|max:100',
            'pais' => 'required|string|max:56',
        ]);


        // Criar a encomenda com os dados de endereço
        $carrinho = $user->carrinho;
        $total = 0;
        if ($carrinho) {
            foreach ($carrinho->itens as $item) {
                $total += $item->preco_unitario * $item->quantidade;
            }
        }

        $data = [
            'carrinho_id' => $carrinho->id,
            'user_id' => $user->id,
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'porta' => $request->porta,
            'localidade' => $request->localidade,
            'codigo_postal' => $request->codigo_postal,
            'concelho' => $request->concelho,
            'pais' => $request->pais,
            'status' => 'pendente',
            'total' => $total,
        ];
        $encomenda = $user->encomendas()->create($data);

        // Adicionar os itens do carrinho à encomenda
        if ($carrinho) {
            foreach ($carrinho->itens as $item) {
                $encomenda->itens()->create([
                    'livro_id' => $item->livro_id,
                    'quantidade' => $item->quantidade,
                ]);
            }
            // Limpar o carrinho
            $carrinho->itens()->delete();
        }
    return redirect('/meus-pedidos')->with('success', 'Pedido #' . $encomenda->id . ' realizado com sucesso!');
    }
}
