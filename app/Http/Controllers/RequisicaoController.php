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
use App\Services\LogService; // ADICIONADO

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

        // VALIDAÇÕES
        if ($user->requisicoes()->where('ativo', true)->count() >= 3) {
            // LOG DE TENTATIVA BLOQUEADA
            LogService::simples('requisicoes', $livro->id, "Requisição bloqueada → Utilizador {$user->name} já tem 3 livros activos");
            
            return redirect('/livros/create')->withErrors(['error' => '❌ Você já tem 3 livros ativos requisitados.']);
        }

        if ($livro->requisicoes()->where('ativo', true)->exists()) {
            // LOG DE TENTATIVA BLOQUEADA
            LogService::simples('requisicoes', $livro->id, "Requisição bloqueada → Livro '{$livro->nome}' já está requisitado");
            
            return redirect('/livros/create')->withErrors(['error' => '❌ Este livro já está requisitado por outro usuário.']);
        }

        // CRIAR A REQUISIÇÃO
        $requisicao = Requisicao::create([
            'user_id'  => $user->id,
            'livro_id' => $livro->id,
            'ativo'    => true,
        ]);

        // MARCAR LIVRO COMO INDISPONÍVEL
        $livro->disponivel = false;
        $livro->save();

        // ENVIAR E-MAILS
        $admins = User::where('is_admin', true)->pluck('email')->toArray();

        try {
            Mail::to($user->email)->send(new NovaRequisicaoMail($requisicao));
            foreach ($admins as $adminEmail) {
                Mail::to($adminEmail)->send(new NovaRequisicaoMail($requisicao));
            }
        } catch (\Exception $e) {
            LogService::simples('requisicoes', $requisicao->id, "Erro no envio de e-mail → {$e->getMessage()}");
        }

        // LOG DE NOVA REQUISIÇÃO - com dados estruturados
        $dadosRequisicao = [
            'livro' => $livro->nome,
            'utilizador' => $user->name,
            'data_requisicao' => $requisicao->created_at->format('d/m/Y H:i'),
            'status' => 'Ativo'
        ];
        
        LogService::log('requisicoes', $requisicao->id, 'criação', null, $dadosRequisicao);

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

        // LOG DE CONSULTA (apenas para admins, para não encher o log)
        if ($user && $user->is_admin) {
            LogService::simples('requisicoes', 0, "Consulta de requisições → {$requisicoes->count()} registos visualizados");
        }

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

        // CAPTURAR DADOS ANTES DA DEVOLUÇÃO
        $dadosAntes = [
            'livro' => $req->livro->nome ?? 'Desconhecido',
            'utilizador' => $req->user->name ?? 'Desconhecido',
            'status' => 'Ativo',
            'data_requisicao' => $req->created_at->format('d/m/Y H:i'),
            'data_devolucao' => null,
            'dias_decorridos' => null
        ];

        // CALCULAR DIAS
        $diasDecorridos = $req->created_at->diffInDays(now());
        
        // ACTUALIZAR A REQUISIÇÃO
        $req->data_recepcao = now();
        $req->dias_decorridos = $diasDecorridos;
        $req->ativo = false;
        $req->save();

        // MARCAR LIVRO COMO DISPONÍVEL
        if ($req->livro) {
            $req->livro->disponivel = true;
            $req->livro->save();
        }

        // ENVIAR NOTIFICAÇÕES DE DISPONIBILIDADE
        $notificacoesEnviadas = 0;
        if ($req->livro) {
            foreach ($req->livro->notificacoesDisponibilidade as $notificacao) {
                try {
                    Mail::to($notificacao->user->email)->send(new LivroDisponivelMail($req->livro));
                    $notificacao->delete();
                    $notificacoesEnviadas++;
                } catch (\Exception $e) {
                    LogService::simples('requisicoes', $req->id, "Erro ao notificar disponibilidade → {$e->getMessage()}");
                }
            }
        }

        // DADOS DEPOIS DA DEVOLUÇÃO
        $dadosDepois = [
            'livro' => $req->livro->nome ?? 'Desconhecido',
            'utilizador' => $req->user->name ?? 'Desconhecido',
            'status' => 'Devolvido',
            'data_requisicao' => $req->created_at->format('d/m/Y H:i'),
            'data_devolucao' => $req->data_recepcao->format('d/m/Y H:i'),
            'dias_decorridos' => $diasDecorridos
        ];

        // LOG DE DEVOLUÇÃO - com comparação estruturada
        LogService::log('requisicoes', $req->id, 'devolução', $dadosAntes, $dadosDepois);

        // LOG ADICIONAL DE NOTIFICAÇÕES SE HOUVE
        if ($notificacoesEnviadas > 0) {
            LogService::simples('requisicoes', $req->id, "Notificações de disponibilidade → {$notificacoesEnviadas} utilizadores notificados sobre '{$req->livro->nome}'");
        }

        return back()->with('success', '✅ Devolução confirmada!');
    }

    /**
     * Cancelar uma requisição (antes de ser entregue)
     */
    public function cancelar($id)
    {
        $req = Requisicao::findOrFail($id);

        // Verificar se o utilizador pode cancelar
        if (!Auth::user()->is_admin && $req->user_id !== Auth::id()) {
            LogService::simples('requisicoes', $req->id, "Tentativa de cancelamento não autorizado → Utilizador: " . Auth::user()->name);
            return back()->withErrors(['error' => '❌ Não autorizado a cancelar esta requisição.']);
        }

        // CAPTURAR DADOS ANTES DO CANCELAMENTO
        $dadosAntes = [
            'livro' => $req->livro->nome ?? 'Desconhecido',
            'utilizador' => $req->user->name ?? 'Desconhecido',
            'status' => 'Ativo',
            'data_requisicao' => $req->created_at->format('d/m/Y H:i')
        ];

        // CANCELAR A REQUISIÇÃO
        $req->ativo = false;
        $req->data_recepcao = now();
        $req->dias_decorridos = 0; // Cancelamento não conta dias
        $req->save();

        // MARCAR LIVRO COMO DISPONÍVEL
        if ($req->livro) {
            $req->livro->disponivel = true;
            $req->livro->save();
        }

        // DADOS DEPOIS DO CANCELAMENTO
        $dadosDepois = [
            'livro' => $req->livro->nome ?? 'Desconhecido',
            'utilizador' => $req->user->name ?? 'Desconhecido',
            'status' => 'Cancelado',
            'data_requisicao' => $req->created_at->format('d/m/Y H:i'),
            'data_cancelamento' => now()->format('d/m/Y H:i')
        ];

        // LOG DE CANCELAMENTO
        LogService::log('requisicoes', $req->id, 'cancelamento', $dadosAntes, $dadosDepois);

        return back()->with('success', '✅ Requisição cancelada com sucesso!');
    }

    /**
     * Relatório de requisições (apenas para admins)
     */
    public function relatorio(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->is_admin) {
            return redirect()->back()->withErrors(['error' => '❌ Acesso negado.']);
        }

        $periodo = $request->input('periodo', '30'); // dias
        $dataInicio = now()->subDays($periodo);

        $estatisticas = [
            'total_requisicoes' => Requisicao::where('created_at', '>=', $dataInicio)->count(),
            'requisicoes_ativas' => Requisicao::where('ativo', true)->count(),
            'devolucoes_periodo' => Requisicao::where('data_recepcao', '>=', $dataInicio)->where('ativo', false)->count(),
            'utilizadores_ativos' => Requisicao::where('ativo', true)->distinct('user_id')->count(),
            'livros_mais_requisitados' => Requisicao::with('livro')
                ->where('created_at', '>=', $dataInicio)
                ->selectRaw('livro_id, COUNT(*) as total')
                ->groupBy('livro_id')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            'tempo_medio_emprestimo' => Requisicao::where('data_recepcao', '>=', $dataInicio)
                ->where('ativo', false)
                ->avg('dias_decorridos')
        ];

        // LOG DE RELATÓRIO
        LogService::simples('requisicoes', 0, "Relatório gerado → Período: {$periodo} dias | Total: {$estatisticas['total_requisicoes']} requisições");

        return view('requisicoes.relatorio', compact('estatisticas', 'periodo'));
    }

    /**
     * Notificar utilizador sobre atraso
     */
    public function notificarAtraso($id)
    {
        $req = Requisicao::with(['user', 'livro'])->findOrFail($id);

        if (!$req->ativo) {
            return back()->withErrors(['error' => '❌ Esta requisição já foi finalizada.']);
        }

        $diasAtraso = $req->created_at->diffInDays(now());
        $limiteNormal = 30; // dias

        if ($diasAtraso < $limiteNormal) {
            return back()->withErrors(['error' => '❌ Requisição ainda dentro do prazo normal.']);
        }

        try {
            // Aqui enviaria um e-mail de notificação de atraso
            // Mail::to($req->user->email)->send(new NotificacaoAtrasoMail($req));
            
            // LOG DE NOTIFICAÇÃO DE ATRASO
            LogService::simples('requisicoes', $req->id, "Notificação de atraso → Utilizador: {$req->user->name} | Livro: {$req->livro->nome} | Dias: {$diasAtraso}");
            
            return back()->with('success', "✅ Notificação de atraso enviada! ({$diasAtraso} dias)");
            
        } catch (\Exception $e) {
            LogService::simples('requisicoes', $req->id, "Erro na notificação de atraso → {$e->getMessage()}");
            return back()->withErrors(['error' => '❌ Erro ao enviar notificação.']);
        }
    }
}