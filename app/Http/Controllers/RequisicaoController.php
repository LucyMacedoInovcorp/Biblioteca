<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Mail\NovaRequisicaoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\LivroDisponivelMail;





class RequisicaoController extends Controller
{
    /**
     * Registrar uma nova requisição de livro
     */
    public function store(Livro $livro)
    {
        if (!Auth::check()) {
            return redirect('/livros/create')->withErrors(['error' => '❌ Você precisa estar autenticado para requisitar um livro.']);
        }

        $user = Auth::user();

        if ($user->requisicoes()->where('ativo', true)->count() >= 3) {
            return redirect('/livros/create')->withErrors(['error' => '❌ Você já tem 3 livros ativos requisitados.']);
        }

        if ($livro->requisicoes()->where('ativo', true)->exists()) {
            return redirect('/livros/create')->withErrors(['error' => '❌ Este livro já está requisitado por outro usuário.']);
        }

        $requisicao = Requisicao::create([
            'user_id'  => $user->id,
            'livro_id' => $livro->id,
            'ativo'    => true,
        ]);

        $admins = User::where('is_admin', true)->pluck('email')->toArray();

        Mail::to($user->email)->send(new NovaRequisicaoMail($requisicao));
        foreach ($admins as $adminEmail) {
            Mail::to($adminEmail)->send(new NovaRequisicaoMail($requisicao));
        }

    return redirect('/livros/create')->with('success', '✅ Requisição realizada com sucesso! Um e-mail de confirmação foi enviado.');
    }

    /**
     * Listar requisições do usuário (ou todas, se admin)
     */
    public function index()
    {
        $user = Auth::user();

        $requisicoesQuery = Requisicao::with(['livro', 'user'])
            ->when($user && !$user->is_admin, function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        $requisicoes = (clone $requisicoesQuery)
            ->orderBy('created_at', 'desc')
            ->get();

        $ativas = (clone $requisicoesQuery)
            ->where('ativo', true)
            ->count();

        $ultimos30dias = (clone $requisicoesQuery)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $entreguesHoje = (clone $requisicoesQuery)
            ->whereDate('data_recepcao', today())
            ->count();

        return view('requisicoes.index', compact(
            'requisicoes',
            'ativas',
            'ultimos30dias',
            'entreguesHoje'
        ));
    }

    /**
     * Confirmar devolução de um livro
     */
    public function confirmarRececao($id)
    {
        $req = Requisicao::findOrFail($id);

        $req->data_recepcao  = now();
        $req->dias_decorridos = $req->created_at->diffInDays(now());
        $req->ativo = false;
        $req->save();

        if ($req->livro) {
            $req->livro->disponivel = true;
            $req->livro->save();
        }


        if ($req->livro) {
            foreach ($req->livro->notificacoesDisponibilidade as $notificacao) {
                Mail::to($notificacao->user->email)->send(new LivroDisponivelMail($req->livro));
                $notificacao->delete(); 
            }
        }

        return back()->with('success', '✅ Devolução confirmada!');
    }
}
