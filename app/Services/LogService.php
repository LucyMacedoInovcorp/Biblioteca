<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

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
                'editora_id' => 'Editora'
            ];
            
            foreach ($dadosNovos as $campo => $valorNovo) {
                if (isset($dadosAnteriores[$campo])) {
                    $valorAntigo = $dadosAnteriores[$campo];
                    
                    // Só regista se houve mudança real
                    if ($valorAntigo != $valorNovo) {
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
                        }

                        // Limitar texto muito longo
                        if (in_array($campo, ['bibliografia', 'description']) && strlen($valorAntigo) > 50) {
                            $valorAntigo = substr($valorAntigo, 0, 47) . '...';
                        }
                        if (in_array($campo, ['bibliografia', 'description']) && strlen($valorNovo) > 50) {
                            $valorNovo = substr($valorNovo, 0, 47) . '...';
                        }
                        
                        // Proteger valores vazios
                        $valorAntigo = $valorAntigo ?: '(vazio)';
                        $valorNovo = $valorNovo ?: '(vazio)';
                        
                        $alteracoes[] = "{$campoNome} → de: {$valorAntigo} | para: {$valorNovo}";
                    }
                }
            }
            
            if (empty($alteracoes)) {
                return "Actualização → sem alterações significativas";
            }
            
            return "Actualização → " . implode(' | ', $alteracoes);
        }
        
        // Fallback para acções simples
        return ucfirst($acao);
    }

    /**
     * Detectar browser de forma mais precisa
     */
    private static function detectarBrowser()
    {
        $userAgent = request()->userAgent();
        
        // Detecção mais precisa dos browsers
        if (preg_match('/Edg\//', $userAgent)) {
            return 'Microsoft Edge';
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
}