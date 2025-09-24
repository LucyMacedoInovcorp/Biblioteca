<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;



/*------------------------INCLUSÃO, ALTERAÇÃO E EXCLUSÃO DE REGISTOS------------------------*/

class LogService
{
    public static function log($modulo, $objetoId, $acao, $dadosAnteriores = null, $dadosNovos = null)
    {
        $alteracao = self::formatarAlteracao($acao, $dadosAnteriores, $dadosNovos);

        Log::create([
            'data_hora' => now(),
            'user_id' => Auth::id(),
            'modulo' => $modulo,
            'objeto_id' => $objetoId,
            'alteracao' => $alteracao,
            'ip' => request()->ip(),
            'browser' => self::detectarBrowser(),
        ]);
    }

    private static function formatarAlteracao($acao, $dadosAnteriores, $dadosNovos)
    {
        // VERIFICA SE HÁ DESCRIÇÃO PERSONALIZADA
        if (!is_null($dadosNovos) && isset($dadosNovos['_descricao_personalizada'])) {
            return $dadosNovos['_descricao_personalizada'];
        }

        // Se for criação
        if (is_null($dadosAnteriores) && !is_null($dadosNovos)) {
            $nomeObjeto = $dadosNovos['nome'] ?? $dadosNovos['name'] ?? $dadosNovos['title'] ?? 'registo';
            return "Criação → {$nomeObjeto}";
        }

        // Se for exclusão
        if (!is_null($dadosAnteriores) && is_null($dadosNovos)) {
            $nomeObjeto = $dadosAnteriores['nome'] ?? $dadosAnteriores['name'] ?? $dadosAnteriores['title'] ?? 'registo';
            return "Exclusão → {$nomeObjeto}";
        }

        // Se for actualização com comparação
        if (!is_null($dadosAnteriores) && !is_null($dadosNovos)) {
            $alteracoes = [];

            // CAMPOS A IGNORAR (timestamps automáticos e campos internos)
            $camposIgnorar = [
                'created_at',
                'updated_at',
                'deleted_at',
                'id',
                'pivot',
                'remember_token',
                'email_verified_at',
                'laravel_session'
            ];

            // Campos traduzidos para português
            $camposTraducao = [
                'nome' => 'Nome',
                'name' => 'Nome',
                'title' => 'Título',
                'ISBN' => 'ISBN',
                'preco' => 'Preço',
                'bibliografia' => 'Bibliografia',
                'description' => 'Descrição',
                'email' => 'Email',
                'is_admin' => 'Tipo de Utilizador',
                'logotipo' => 'Logótipo',
                'foto' => 'Fotografia',
                'imagemcapa' => 'Capa do Livro',
                'editora_id' => 'Editora',
                'autor_id' => 'Autor',
                'user_id' => 'Utilizador',
                'status' => 'Status',
                'quantidade' => 'Quantidade',
                'data_requisicao' => 'Data de Requisição',
                'data_devolucao' => 'Data de Devolução',
                'password' => 'Palavra-passe',
                'telefone' => 'Telefone',
                'morada' => 'Morada'
            ];

            // FILTRAR E COMPARAR APENAS OS CAMPOS DESEJADOS
            foreach ($dadosNovos as $campo => $valorNovo) {
                // PULAR CAMPOS QUE DEVEM SER IGNORADOS
                if (in_array($campo, $camposIgnorar)) {
                    continue;
                }

                // PULAR CAMPOS QUE COMEÇAM COM UNDERSCORE (internos)
                if (strpos($campo, '_') === 0) {
                    continue;
                }

                if (array_key_exists($campo, $dadosAnteriores)) {
                    $valorAntigo = $dadosAnteriores[$campo];

                    // Só regista se houve mudança real E não são timestamps
                    if ($valorAntigo != $valorNovo && !self::isTimestamp($valorAntigo) && !self::isTimestamp($valorNovo)) {
                        $campoNome = $camposTraducao[$campo] ?? ucfirst(str_replace('_', ' ', $campo));

                        // Tratamento especial para diferentes tipos de campos
                        switch ($campo) {
                            case 'is_admin':
                                $valorAntigo = $valorAntigo ? 'Administrador' : 'Utilizador';
                                $valorNovo = $valorNovo ? 'Administrador' : 'Utilizador';
                                break;

                            case 'preco':
                                $valorAntigo = number_format((float)$valorAntigo, 2, ',', '.') . '€';
                                $valorNovo = number_format((float)$valorNovo, 2, ',', '.') . '€';
                                break;

                            case 'password':
                                $valorAntigo = '••••••••';
                                $valorNovo = '••••••••';
                                break;

                            case 'data_requisicao':
                            case 'data_devolucao':
                                if ($valorAntigo) {
                                    $valorAntigo = date('d/m/Y', strtotime($valorAntigo));
                                }
                                if ($valorNovo) {
                                    $valorNovo = date('d/m/Y', strtotime($valorNovo));
                                }
                                break;

                            case 'editora_id':
                                // Converter ID da editora para nome
                                try {
                                    $editoraAntiga = \App\Models\Editora::find($valorAntigo);
                                    $editoraNova = \App\Models\Editora::find($valorNovo);
                                    $valorAntigo = $editoraAntiga ? $editoraAntiga->nome : "ID: {$valorAntigo}";
                                    $valorNovo = $editoraNova ? $editoraNova->nome : "ID: {$valorNovo}";
                                } catch (\Exception $e) {
                                    $valorAntigo = "ID: {$valorAntigo}";
                                    $valorNovo = "ID: {$valorNovo}";
                                }
                                break;

                            case 'autor_id':
                                // Converter ID do autor para nome
                                try {
                                    $autorAntigo = \App\Models\Autor::find($valorAntigo);
                                    $autorNovo = \App\Models\Autor::find($valorNovo);
                                    $valorAntigo = $autorAntigo ? $autorAntigo->nome : "ID: {$valorAntigo}";
                                    $valorNovo = $autorNovo ? $autorNovo->nome : "ID: {$valorNovo}";
                                } catch (\Exception $e) {
                                    $valorAntigo = "ID: {$valorAntigo}";
                                    $valorNovo = "ID: {$valorNovo}";
                                }
                                break;

                            case 'user_id':
                                // Converter ID do utilizador para nome
                                try {
                                    $userAntigo = \App\Models\User::find($valorAntigo);
                                    $userNovo = \App\Models\User::find($valorNovo);
                                    $valorAntigo = $userAntigo ? $userAntigo->name : "ID: {$valorAntigo}";
                                    $valorNovo = $userNovo ? $userNovo->name : "ID: {$valorNovo}";
                                } catch (\Exception $e) {
                                    $valorAntigo = "ID: {$valorAntigo}";
                                    $valorNovo = "ID: {$valorNovo}";
                                }
                                break;
                        }

                        // Limitar texto muito longo para campos de texto
                        if (in_array($campo, ['bibliografia', 'description']) && strlen($valorAntigo) > 50) {
                            $valorAntigo = substr($valorAntigo, 0, 47) . '...';
                        }
                        if (in_array($campo, ['bibliografia', 'description']) && strlen($valorNovo) > 50) {
                            $valorNovo = substr($valorNovo, 0, 47) . '...';
                        }

                        // Proteger valores vazios
                        $valorAntigo = $valorAntigo ?: '(vazio)';
                        $valorNovo = $valorNovo ?: '(vazio)';

                        $alteracoes[] = "{$campoNome} → de: {$valorAntigo} para: {$valorNovo}";
                    }
                }
            }

            if (empty($alteracoes)) {
                return "Actualização → sem alterações significativas";
            }

            return "Actualização → " . implode(' | ', $alteracoes);
        }

        // Fallback para acções simples (compatibilidade)
        return ucfirst($acao);
    }

    /**
     * Verificar se um valor parece ser um timestamp
     */
    private static function isTimestamp($valor)
    {
        if (is_null($valor)) return false;

        // Verificar se é um timestamp no formato Laravel (YYYY-MM-DD HH:MM:SS)
        if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}/', $valor)) {
            return true;
        }

        // Verificar se é um timestamp Unix
        if (is_numeric($valor) && $valor > 1000000000 && $valor < 2147483647) {
            return true;
        }

        return false;
    }

    /**
     * Detectar browser de forma mais precisa
     */
    private static function detectarBrowser()
    {
        $userAgent = request()->userAgent();

        // Detecção mais precisa dos browsers
        if (preg_match('/Edg\//', $userAgent)) {
            preg_match('/Edg\/([\d.]+)/', $userAgent, $matches);
            $version = $matches[1] ?? 'Desconhecida';
            return "Microsoft Edge {$version}";
        }

        if (preg_match('/Chrome\/[\d.]+/', $userAgent) && !preg_match('/Edg\//', $userAgent)) {
            preg_match('/Chrome\/([\d.]+)/', $userAgent, $matches);
            $version = $matches[1] ?? 'Desconhecida';
            return "Google Chrome {$version}";
        }

        if (preg_match('/Firefox\/([\d.]+)/', $userAgent)) {
            preg_match('/Firefox\/([\d.]+)/', $userAgent, $matches);
            $version = $matches[1] ?? 'Desconhecida';
            return "Mozilla Firefox {$version}";
        }

        if (preg_match('/Safari\//', $userAgent) && !preg_match('/Chrome\//', $userAgent)) {
            preg_match('/Version\/([\d.]+)/', $userAgent, $matches);
            $version = $matches[1] ?? 'Desconhecida';
            return "Safari {$version}";
        }

        if (preg_match('/Opera|OPR\//', $userAgent)) {
            if (preg_match('/OPR\/([\d.]+)/', $userAgent, $matches)) {
                $version = $matches[1] ?? 'Desconhecida';
                return "Opera {$version}";
            }
            return 'Opera';
        }

        // Para browsers desconhecidos ou casos especiais
        return substr($userAgent, 0, 100) . (strlen($userAgent) > 100 ? '...' : '');
    }

    /**
     * Detectar sistema operativo
     */
    private static function detectarSO()
    {
        $userAgent = request()->userAgent();

        if (preg_match('/Windows NT 10/', $userAgent)) return 'Windows 10/11';
        if (preg_match('/Windows NT 6.3/', $userAgent)) return 'Windows 8.1';
        if (preg_match('/Windows NT 6.2/', $userAgent)) return 'Windows 8';
        if (preg_match('/Windows NT 6.1/', $userAgent)) return 'Windows 7';
        if (preg_match('/Mac OS X/', $userAgent)) return 'macOS';
        if (preg_match('/Linux/', $userAgent)) return 'Linux';
        if (preg_match('/Android/', $userAgent)) return 'Android';
        if (preg_match('/iPhone|iPad/', $userAgent)) return 'iOS';

        return 'Sistema Desconhecido';
    }

    /**
     * Obter informações completas do browser
     */
    public static function informacoesBrowser()
    {
        return [
            'browser' => self::detectarBrowser(),
            'so' => self::detectarSO(),
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip()
        ];
    }

    /**
     * Método auxiliar para logs simples (compatibilidade)
     */
    public static function simples($modulo, $objetoId, $mensagem)
    {
        Log::create([
            'data_hora' => now(),
            'user_id' => Auth::id(),
            'modulo' => $modulo,
            'objeto_id' => $objetoId,
            'alteracao' => $mensagem,
            'ip' => request()->ip(),
            'browser' => self::detectarBrowser(),
        ]);
    }

    /**
     * Método para logs de sistema (sem utilizador logado)
     */
    public static function sistema($modulo, $objetoId, $mensagem)
    {
        Log::create([
            'data_hora' => now(),
            'user_id' => null,
            'modulo' => $modulo,
            'objeto_id' => $objetoId,
            'alteracao' => "SISTEMA → {$mensagem}",
            'ip' => request()->ip(),
            'browser' => self::detectarBrowser(),
        ]);
    }

    /**
     * Método para logs de requisições
     */
    public static function requisicao($requisicaoId, $acao, $dadosAnteriores = null, $dadosNovos = null)
    {
        self::log('requisicoes', $requisicaoId, $acao, $dadosAnteriores, $dadosNovos);
    }

    /**
     * Método para logs de utilizadores
     */
    public static function utilizador($userId, $acao, $dadosAnteriores = null, $dadosNovos = null)
    {
        self::log('utilizadores', $userId, $acao, $dadosAnteriores, $dadosNovos);
    }

    /**
     * Método para debug (apenas em desenvolvimento)
     */
    public static function debug($modulo, $objetoId, $mensagem, $dados = null)
    {
        if (config('app.debug')) {
            $mensagemCompleta = $mensagem;
            if ($dados) {
                $mensagemCompleta .= ' | Dados: ' . json_encode($dados);
            }

            self::simples($modulo, $objetoId, "DEBUG → {$mensagemCompleta}");
        }
    }

    /**
     * Método para limpar logs antigos (manutenção)
     */
    public static function limparLogsAntigos($dias = 90)
    {
        $dataLimite = now()->subDays($dias);

        $logsRemovidos = Log::where('data_hora', '<', $dataLimite)->count();
        Log::where('data_hora', '<', $dataLimite)->delete();

        self::sistema('sistema', 0, "Limpeza automática → {$logsRemovidos} logs removidos (>$dias dias)");

        return $logsRemovidos;
    }

    /**
     * Método para estatísticas dos logs
     */
    public static function estatisticas($dias = 30)
    {
        $dataInicio = now()->subDays($dias);

        return [
            'total_logs' => Log::where('data_hora', '>=', $dataInicio)->count(),
            'por_modulo' => Log::where('data_hora', '>=', $dataInicio)
                ->selectRaw('modulo, COUNT(*) as total')
                ->groupBy('modulo')
                ->orderByDesc('total')
                ->get(),
            'por_utilizador' => Log::where('data_hora', '>=', $dataInicio)
                ->with('user')
                ->selectRaw('user_id, COUNT(*) as total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->get(),
            'acoes_mais_comuns' => Log::where('data_hora', '>=', $dataInicio)
                ->selectRaw('LEFT(alteracao, 20) as acao, COUNT(*) as total')
                ->groupBy('acao')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
        ];
    }
}
