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
use Illuminate\Http\JsonResponse;

class RequisicaoController extends Controller
{
    /**
     * Registrar uma nova requisição de livro (Web)
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

        // Verificar se há estoque disponível
        if (!$livro->temEstoque()) {
            return redirect('/livros/create')->withErrors(['error' => '❌ Este livro não possui estoque disponível.']);
        }

        if ($livro->requisicoes()->where('ativo', true)->exists()) {
            return redirect('/livros/create')->withErrors(['error' => '❌ Este livro já está requisitado por outro usuário.']);
        }

        // Reduzir estoque
        if (!$livro->reduzirEstoque()) {
            return redirect('/livros/create')->withErrors(['error' => '❌ Erro ao processar requisição. Tente novamente.']);
        }

        $requisicao = Requisicao::create([
            'user_id'  => $user->id,
            'livro_id' => $livro->id,
            'ativo'    => true,
            'quantidade' => 1, // Assumindo quantidade padrão 1 para requisições web
        ]);

        $admins = User::where('is_admin', true)->pluck('email')->toArray();

        Mail::to($user->email)->send(new NovaRequisicaoMail($requisicao));
        foreach ($admins as $adminEmail) {
            Mail::to($adminEmail)->send(new NovaRequisicaoMail($requisicao));
        }

        return redirect('/livros/create')->with('success', '✅ Requisição realizada com sucesso! Um e-mail de confirmação foi enviado.');
    }

    /**
     * Criar requisição via API/JSON (para os testes)
     */
    public function storeJson(Request $request): JsonResponse
    {
        $request->validate([
            'livro_id' => 'required|exists:livros,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $livro = Livro::findOrFail($request->livro_id);
        $user = Auth::user();
        
        // Verificar autenticação
        if (!$user) {
            return response()->json([
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        // Verificar limite de requisições ativas
        if ($user->requisicoes()->where('ativo', true)->count() >= 3) {
            return response()->json([
                'message' => 'Você já tem 3 livros ativos requisitados'
            ], 422);
        }

        // Verificar se há estoque disponível
        if (!$livro->temEstoque($request->quantidade)) {
            return response()->json([
                'message' => 'Livro sem estoque disponível'
            ], 422);
        }

        // Reduzir estoque
        if (!$livro->reduzirEstoque($request->quantidade)) {
            return response()->json([
                'message' => 'Erro ao processar requisição'
            ], 422);
        }

        // Criar a requisição
        $requisicao = Requisicao::create([
            'livro_id' => $livro->id,
            'user_id' => $user->id,
            'quantidade' => $request->quantidade,
            'ativo' => true,
        ]);

        // Enviar e-mails de notificação
        $admins = User::where('is_admin', true)->pluck('email')->toArray();

        Mail::to($user->email)->send(new NovaRequisicaoMail($requisicao));
        foreach ($admins as $adminEmail) {
            Mail::to($adminEmail)->send(new NovaRequisicaoMail($requisicao));
        }

        return response()->json($requisicao->load(['livro', 'user']), 201);
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

        $req->data_recepcao = now();
        $req->dias_decorridos = $req->created_at->diffInDays(now());
        $req->ativo = false;
        $req->save();

        // Retornar estoque ao livro
        if ($req->livro) {
            $quantidade = $req->quantidade ?? 1; // Usar quantidade da requisição ou 1 como padrão
            $req->livro->adicionarEstoque($quantidade);
        }

        // Enviar notificações para usuários na lista de espera
        if ($req->livro) {
            foreach ($req->livro->notificacoesDisponibilidade as $notificacao) {
                Mail::to($notificacao->user->email)->send(new LivroDisponivelMail($req->livro));
                $notificacao->delete(); 
            }
        }

        return back()->with('success', '✅ Devolução confirmada e estoque atualizado!');
    }

    /**
     * Cancelar uma requisição (retorna estoque)
     */
    public function cancelar($id)
    {
        $req = Requisicao::findOrFail($id);
        
        // Verificar se o usuário pode cancelar esta requisição
        if (!Auth::user()->is_admin && $req->user_id !== Auth::id()) {
            return back()->withErrors(['error' => '❌ Você não tem permissão para cancelar esta requisição.']);
        }

        if (!$req->ativo) {
            return back()->withErrors(['error' => '❌ Esta requisição já foi finalizada.']);
        }

        // Retornar estoque
        if ($req->livro) {
            $quantidade = $req->quantidade ?? 1;
            $req->livro->adicionarEstoque($quantidade);
        }

        // Marcar requisição como cancelada
        $req->ativo = false;
        $req->data_recepcao = now();
        $req->save();

        // Notificar usuários em lista de espera
        if ($req->livro) {
            foreach ($req->livro->notificacoesDisponibilidade as $notificacao) {
                Mail::to($notificacao->user->email)->send(new LivroDisponivelMail($req->livro));
                $notificacao->delete(); 
            }
        }

        return back()->with('success', '✅ Requisição cancelada e estoque restaurado!');
    }
}