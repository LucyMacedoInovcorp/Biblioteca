<?php
namespace App\Http\Controllers;

use App\Models\Avaliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\ReviewResultMail;
use Illuminate\Support\Facades\Mail;

class AvaliacaoController extends Controller
{
    // Exibe o formulário de avaliação
    public function create(Request $request)
    {
        $livro_id = $request->input('livro_id');
        $requisicao_id = $request->input('requisicao_id');
        return view('avaliacoes.create', compact('livro_id', 'requisicao_id'));
}

// Salvar nova avaliação
    public function store(Request $request)
    {    
        $request->validate([
            'review' => 'required|string',
            'rating' => 'required|integer|min:1|max:10',
            'livro_id' => 'required|exists:livros,id',
            'requisicao_id' => 'required|exists:requisicoes,id',
        ]);

        $jaAvaliada = Avaliacao::where('user_id', Auth::id())
            ->where('requisicao_id', $request->requisicao_id)
            ->exists();

        if ($jaAvaliada) {
            return redirect()->back()->withErrors(['Você já avaliou esta requisição.']);
        }


            $avaliacao = Avaliacao::create([
                'user_id' => Auth::id(),
                'livro_id' => $request->livro_id,
                'requisicao_id' => $request->requisicao_id,
                'review' => $request->review,
                'rating' => $request->rating,
                'status' => 'suspenso',
            ]);

            // Enviar e-mail para administradores usando Mailable
            $admins = \App\Models\User::where('is_admin', true)->pluck('email');
            foreach ($admins as $adminEmail) {
                \Mail::to($adminEmail)->send(new \App\Mail\SendReviewMail($avaliacao));
            }

            return redirect()->back()->with('success', 'Avaliação enviada!');
}

    /*----------Listar avaliações pendentes para o admin aprovar----------*/
    public function pendentes()
    {
    $avaliacoes = Avaliacao::where('status', 'suspenso')->get();
    return view('avaliacoes.suspensas', compact('avaliacoes'));
    }

    // Aprovar avaliação
    public function aprovar($id)
    {
    $avaliacao = Avaliacao::findOrFail($id);
    $avaliacao->status = 'ativo';
    $avaliacao->save();

    // Enviar email para o cidadão informando que a avaliação foi aprovada
    $user = $avaliacao->user;
    \Mail::to($user->email)->send(new \App\Mail\ReviewResultMail('ativa'));

    return redirect()->back()->with('success', 'Avaliação aprovada!');
    }

    // Rejeitar avaliação
    public function rejeitar(Request $request, $id)
    {
    $avaliacao = Avaliacao::findOrFail($id);
    $avaliacao->status = 'recusado';
    $avaliacao->justificativa_recusa = $request->input('justificativa_recusa');
    $avaliacao->save();

    // Enviar email para o cidadão informando que a avaliação foi recusada e a justificativa
    $user = $avaliacao->user;
    \Mail::to($user->email)->send(new \App\Mail\ReviewResultMail('recusada', $avaliacao->justificativa_recusa));

    return redirect()->back()->with('success', 'Avaliação recusada com justificativa!');
    }
}
