<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Support\Facades\Auth;


class RequisicaoController extends Controller
{
    public function store(Livro $livro)
    {
        // Garante que o usuário está logado
        if (!Auth::check()) {
            return back()->with('error', '❌ Você precisa estar autenticado para requisitar um livro.');
        }

        $user = Auth::user(); // aqui você já sabe que existe um usuário

        // 1. Verificar limite
        if ($user->requisicoes()->where('ativo', true)->count() >= 3) {
            return back()->with('error', '❌ Você já tem 3 livros ativos requisitados.');
        }


        // 2. Verificar se livro já foi requisitado
        if ($livro->requisicoes()->where('ativo', true)->exists()) {
            return back()->with('error', '❌ Este livro já está requisitado por outro usuário.');
        }

        // 3. Criar requisição
        Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'ativo' => true,
        ]);

        return back()->with('success', '✅ Requisição realizada com sucesso!');
    }




public function index()
{
    $user = Auth::user(); // sempre existe porque a rota tem middleware('auth')

    if ($user->isAdmin()) {
        // Admin vê todas
        $requisicoes = Requisicao::with(['livro', 'user'])->get();
    } else {
        // Cidadão só vê as dele
        $requisicoes = Requisicao::with(['livro', 'user'])
            ->where('user_id', $user->id)
            ->get();
    }

    return view('requisicoes.index', compact('requisicoes'));
}

}