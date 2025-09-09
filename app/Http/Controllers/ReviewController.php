<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Avaliacao;
use App\Models\Livro;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Exibe o formulário para criar uma nova avaliação
    public function createAvaliacao(Request $request)
    {
        $livro_id = $request->query('livro_id');
        $requisicao_id = $request->query('requisicao_id');
        return view('avaliacoes.create', compact('livro_id', 'requisicao_id'));
    }


    public function store(Request $request)
    {
        $user = Auth::user();
        $livro = Livro::findOrFail($request->input('livro_id'));

        // Cria uma nova avaliação
        $avaliacao = new Avaliacao();
        $avaliacao->livro_id = $livro->id;
        $avaliacao->user_id  = $user->id;
        $avaliacao->review = $request->input('review');
        $avaliacao->rating = $request->input('rating');
        $avaliacao->save();

        // Envia email para os administradores usando a view correta
        $admins = \App\Models\User::where('is_admin', true)->pluck('email');
        foreach ($admins as $adminEmail) {
            Mail::send('emails.reviews.sendreview', [
                'avaliacao' => $avaliacao,
                'livro' => $livro,
                'user' => $user
            ], function ($message) use ($adminEmail, $livro) {
                $message->to($adminEmail)
                        ->subject('Nova avaliação para o livro: ' . $livro->nome);
            });
        }

        return back()->with('success', 'Avaliação realizada com sucesso! Um email de confirmação foi enviado aos administradores.');
    }
}
