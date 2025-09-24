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
use App\Services\LogService;

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
            'quantidade' => 1,
        ]);

        /*------------------------LOG DA REQUISIÇÃO------------------------*/
        LogService::log('requisicoes', $requisicao->id, 'criação', null, [
            'livro' => $livro->nome,
            'usuario' => $user->name,
            'quantidade' => 1,
            'estoque_restante' => $livro->fresh()->estoque
        ]);

        $admins = User::where('is_admin', true)->pluck('email')->toArray();

        Mail::to($user->email)->send(new NovaRequisicaoMail($requisicao));
        foreach ($admins as $adminEmail) {
            Mail::to($adminEmail)->send(new NovaRequisicaoMail($requisicao));
        }

        return redirect('/livros/create')->with('success', '✅ Requisição realizada com sucesso! Um e-mail de confirmação foi enviado.');
    }

    /*------------------------Criar requisição via API/JSON (para os testes)------------------------*/
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

        /*------------------ LOG DA REQUISIÇÃO VIA API ------------------*/
        LogService::log('requisicoes', $requisicao->id, 'criação', null, [
            'livro' => $livro->nome,
            'usuario' => $user->name,
            'quantidade' => $request->quantidade,
            'estoque_restante' => $livro->fresh()->estoque,
            'via' => 'API'
        ]);



        // Enviar e-mails de notificação
        $admins = User::where('is_admin', true)->pluck('email')->toArray();

        Mail::to($user->email)->send(new NovaRequisicaoMail($requisicao));
        foreach ($admins as $adminEmail) {
            Mail::to($adminEmail)->send(new NovaRequisicaoMail($requisicao));
        }

        return response()->json($requisicao->load(['livro', 'user']), 201);
    }


    /*--------------- Listar requisições do usuário (ou todas, se admin) ----------------*/

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

        /*------------------------LOG PARA CAPTURAR DADOS ANTES DA DEVOLUÇÃO------------------------*/
        $dadosAnteriores = [
            'livro' => $req->livro->nome,
            'usuario' => $req->user->name,
            'quantidade' => $req->quantidade ?? 1,
            'ativo' => true,
            'estoque_antes' => $req->livro->estoque
        ];

        $req->data_recepcao = now();
        $req->dias_decorridos = $req->created_at->diffInDays(now());
        $req->ativo = false;
        $req->save();


        // Retornar estoque ao livro
        if ($req->livro) {
            $quantidade = $req->quantidade ?? 1;
            $req->livro->adicionarEstoque($quantidade);
        }

        /*--------------LOG DE DADOS APÓS DEVOLUÇÃO----------------*/
        $dadosNovos = [
            'livro' => $req->livro->nome,
            'usuario' => $req->user->name,
            'quantidade' => $req->quantidade ?? 1,
            'ativo' => false,
            'dias_decorridos' => $req->dias_decorridos,
            'estoque_depois' => $req->livro->fresh()->estoque,
            'processado_por' => Auth::user()->name,
            '_descricao_personalizada' => "Devolução: \"{$req->livro->nome}"
        ];

        /*--------------LOG DE DEVOLUÇÃO----------------*/
        LogService::log('requisicoes', $req->id, 'devolução', $dadosAnteriores, $dadosNovos);

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


        /*------------------------LOG PARA CAPTURAR DADOS ANTES DO CANCELAMENTO------------------------*/
        $dadosAnteriores = [
            'livro' => $req->livro->nome,
            'usuario' => $req->user->name,
            'quantidade' => $req->quantidade ?? 1,
            'ativo' => true,
            'estoque_antes' => $req->livro->estoque
        ];

        // Retornar estoque
        if ($req->livro) {
            $quantidade = $req->quantidade ?? 1;
            $req->livro->adicionarEstoque($quantidade);
        }

        // Marcar requisição como cancelada
        $req->ativo = false;
        $req->data_recepcao = now();
        $req->save();


        /*------------------------LOG DE DADOS APÓS CANCELAMENTO------------------------*/
        $dadosNovos = [
            'livro' => $req->livro->nome,
            'usuario' => $req->user->name,
            'quantidade' => $req->quantidade ?? 1,
            'ativo' => false,
            'estoque_depois' => $req->livro->fresh()->estoque,
            'cancelado_por' => Auth::user()->name,
            'motivo' => 'Cancelamento manual',
            '_descricao_personalizada' => "Cancelamento: \"{$req->livro->nome}"
        ];

        /*------------------------LOG DE CANCELAMENTO------------------------*/
        LogService::log('requisicoes', $req->id, 'cancelamento', $dadosAnteriores, $dadosNovos);

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
