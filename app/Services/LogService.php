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
            'browser' => request()->userAgent(),
        ]);
    }

    private static function formatarAlteracao($acao, $dadosAnteriores, $dadosNovos)
    {
        $alteracao = ucfirst($acao);
        
        if ($dadosAnteriores && $dadosNovos) {
            $diferencas = [];
            foreach ($dadosNovos as $campo => $valorNovo) {
                $valorAntigo = $dadosAnteriores[$campo] ?? null;
                if ($valorAntigo != $valorNovo) {
                    $diferencas[] = "{$campo}: '{$valorAntigo}' → '{$valorNovo}'";
                }
            }
            if (!empty($diferencas)) {
                $alteracao .= ' - ' . implode(', ', $diferencas);
            }
        }
        
        return $alteracao;
    }
}