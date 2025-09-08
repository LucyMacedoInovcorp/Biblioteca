<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class ReviewController extends Controller
{
    // Exibe o formulário para criar uma nova avaliação
    public function createAvaliacao(Request $request)
    {
        $livro_id = $request->query('livro_id');
        $requisicao_id = $request->query('requisicao_id');
        return view('avaliacoes.create', compact('livro_id', 'requisicao_id'));
    }
}
