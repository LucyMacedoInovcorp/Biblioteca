<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Encomenda;

class EncomendaController extends Controller
{
    // Pedidos do usuário autenticado
    public function meusPedidos()
    {
        $user = Auth::user();
        $encomendas = Encomenda::with('itens.livro')->where('user_id', $user->id)->orderByDesc('created_at')->get();
        return view('encomendas.meus', compact('encomendas'));
    }

    // Todos os pedidos para admin
    public function todosPedidos()
    {
    $encomendas = Encomenda::with('user', 'itens.livro')->orderByDesc('created_at')->paginate(10);
    return view('encomendas.todos', compact('encomendas'));
    }
}
