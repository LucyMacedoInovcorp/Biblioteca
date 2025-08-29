<?php

namespace App\Http\Controllers;


use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Support\Facades\Auth;
//Envio por email
use App\Mail\NovaRequisicaoMail;
use Illuminate\Support\Facades\Mail;


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
        $requisicao = Requisicao::create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'ativo' => true,
        ]);

        // 4. Enviar email para o cidadão
        Mail::to($user->email)->send(new NovaRequisicaoMail($requisicao));

        // 5. Enviar email para os administradores
        $admins = \App\Models\User::where('is_admin', true)->pluck('email');
        foreach ($admins as $adminEmail) {
            Mail::to($adminEmail)->send(new NovaRequisicaoMail($requisicao));
        }

        return back()->with('success', '✅ Requisição realizada com sucesso! Um email de confirmação foi enviado.');
    }


    public function index()
    {
        $user = Auth::user();

        $requisicoesQuery = Requisicao::with(['livro', 'user'])
            ->when($user && !$user->is_admin, function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        // Pega todas as requisições (com ordenação)
        $requisicoes = (clone $requisicoesQuery)
            ->orderBy('created_at', 'desc')
            ->get();

        // Indicadores
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



    public function confirmarRececao($id)
    {
        $req = Requisicao::findOrFail($id);

        // data de recepção (hoje)
        $req->data_recepcao = now();

        // calcular os dias decorridos
        $req->dias_decorridos = $req->created_at->diffInDays(now());

        // marcar como finalizada
        $req->ativo = false;
        $req->save();

        // livro volta a estar disponível
        if ($req->livro) {
            $req->livro->disponivel = true;
            $req->livro->save();
        }

        return back()->with('success', '✅ Devolução confirmada!');
    }
}
