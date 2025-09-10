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
        // 1. Verifica autenticação
        if (!Auth::check()) {
            return back()->with('error', '❌ Você precisa estar autenticado para requisitar um livro.');
        }

        $user = Auth::user();

        // 2. Verificar limite de 3 livros ativos
        if ($user->requisicoes()->where('ativo', true)->count() >= 3) {
            return back()->with('error', '❌ Você já tem 3 livros ativos requisitados.');
        }

        // 3. Verificar se o livro já está requisitado
        if ($livro->requisicoes()->where('ativo', true)->exists()) {
            return back()->with('error', '❌ Este livro já está requisitado por outro usuário.');
        }

        // 4. Criar a requisição
        $requisicao = Requisicao::create([
            'user_id'  => $user->id,
            'livro_id' => $livro->id,
            'ativo'    => true,
        ]);

        // 5. Enviar e-mails
        $admins = User::where('is_admin', true)->pluck('email')->toArray();

        // Email para o utilizador
        Mail::to($user->email)
            ->send(new NovaRequisicaoMail($requisicao));

        // Email para cada administrador
        foreach ($admins as $adminEmail) {
            Mail::to($adminEmail)
                ->send(new NovaRequisicaoMail($requisicao));
        }

        return back()->with('success', '✅ Requisição realizada com sucesso! Um e-mail de confirmação foi enviado.');
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
