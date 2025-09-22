<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user')->orderBy('data_hora', 'desc');

        // Filtros
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_hora', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_hora', '<=', $request->data_fim);
        }

        $logs = $query->paginate(50);
        
        // Para os filtros
        $modulos = Log::distinct()->pluck('modulo');
        $usuarios = \App\Models\User::all();

        return view('logs.index', compact('logs', 'modulos', 'usuarios'));
    }
}