<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sugestao;
use Illuminate\Support\Facades\Auth;

class SugestaoController extends Controller
{
    public function storeFromApi(Request $request)
    {
        // Validação básica
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'authors' => 'nullable|array',
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:255',
        ]);

        // Salvar sugestão
        $sugestao = new Sugestao();
        $sugestao->nome = $validated['title'];
        $sugestao->isbn = $validated['isbn'] ?? null;
        $sugestao->autor = isset($validated['authors']) ? implode(', ', $validated['authors']) : null;
        $sugestao->editora = $validated['publisher'] ?? null;
        $sugestao->bibliografia = $validated['description'] ?? null;
        $sugestao->imagemcapa = $validated['cover_image'] ?? null;
        $sugestao->user_id = Auth::id();

        $sugestao->save();

        return redirect()->back()->with('msg', 'Sugestão enviada com sucesso! ✅');
    }
}
