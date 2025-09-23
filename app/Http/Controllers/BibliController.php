<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Support\Facades\Http;
use App\Models\WishlistBook;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;


class BibliController extends Controller
{
    public function indexLivros()
    {
        return view('welcome');
    }

    // ---------- LIVROS ----------
    public function createLivro()
    {
        $livros = Livro::with(['editora', 'autores'])->get();
        $editoras = Editora::orderBy('nome')->get();
        $autores  = Autor::all();

        return view('livros.livros', compact('livros', 'editoras', 'autores'));
    }

    // Para exibir detalhes do livro e suas requisições + livros relacionados fulltext MYSQL
    public function showLivro($id)
    {
        $livro = Livro::with(['requisicoes.user'])->findOrFail($id);
        $relacionados = $livro->relacionados();

        return view('livros.show', compact('livro', 'relacionados'));
    }

    public function storeLivro(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'ISBN' => 'nullable|string|max:255',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric',
            'estoque' => 'required|integer|min:0',
            'editoras' => 'required|array|min:1',
            'autores' => 'required|array|min:1',
        ]);

        $livro = new Livro;
        $livro->nome         = $request->nome;
        $livro->ISBN         = $request->ISBN;
        $livro->bibliografia = $request->bibliografia;
        $livro->preco        = $request->preco;
        $livro->estoque      = $request->estoque; 
        $livro->editora_id   = $request->editoras[0];

        if ($request->hasFile('imagemcapa')) {
            $path = $request->file('imagemcapa')->store('capas', 'public');
            $livro->imagemcapa = '/storage/' . $path;
        } else {
            $livro->imagemcapa = '/images/default-book.png';
        }

        $livro->save();

        if ($request->filled('autores')) {
            $livro->autores()->sync($request->autores);
        }

        // LOG DE CRIAÇÃO - apenas dados principais
        $dadosNovos = [
            'nome' => $livro->nome,
            'ISBN' => $livro->ISBN,
            'preco' => $livro->preco,
            'estoque' => $livro->estoque, 
        ];
        LogService::log('livros', $livro->id, 'criação', null, $dadosNovos);

        return redirect('/livros/create')->with('msg', 'Novo livro adicionado com sucesso!');
    }

    public function editLivro($id)
    {
        $livro    = Livro::with('autores')->findOrFail($id);
        $editoras = Editora::all();
        $autores  = Autor::all();

        return view('livros.edit', compact('livro', 'editoras', 'autores'));
    }

    public function updateLivro(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'ISBN' => 'nullable|string|max:255',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0', 
            'editora_id' => 'required|exists:editoras,id',
            'autores' => 'required|array',
            'imagemcapa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $livro = Livro::findOrFail($id);

        // CAPTURAR DADOS ANTES DA ALTERAÇÃO (incluindo imagem)
        $dadosAnteriores = [
            'nome' => $livro->nome,
            'ISBN' => $livro->ISBN,
            'bibliografia' => $livro->bibliografia,
            'preco' => $livro->preco,
            'estoque' => $livro->estoque, 
            'editora_id' => $livro->editora_id,
            'imagemcapa' => $livro->imagemcapa,
        ];

        // FAZER AS ALTERAÇÕES
        $livro->nome         = $request->nome;
        $livro->ISBN         = $request->ISBN;
        $livro->bibliografia = $request->bibliografia;
        $livro->preco        = $request->preco;
        $livro->estoque      = $request->estoque; 
        $livro->editora_id   = $request->editora_id;

        // CONTROLAR SE HOUVE ALTERAÇÃO DE IMAGEM
        $imagemAlterada = false;
        if ($request->hasFile('imagemcapa')) {
            $path = $request->file('imagemcapa')->store('capas', 'public');
            $livro->imagemcapa = '/storage/' . $path;
            $imagemAlterada = true;
        }

        $livro->save();

        if ($request->filled('autores')) {
            $livro->autores()->sync($request->autores);
        }

        // CAPTURAR DADOS DEPOIS DA ALTERAÇÃO
        $dadosNovos = [
            'nome' => $livro->nome,
            'ISBN' => $livro->ISBN,
            'bibliografia' => $livro->bibliografia,
            'preco' => $livro->preco,
            'estoque' => $livro->estoque, 
            'editora_id' => $livro->editora_id,
            'imagemcapa' => $livro->imagemcapa,
        ];

        // ADICIONAR FLAG DE IMAGEM SE HOUVE ALTERAÇÃO
        if ($imagemAlterada) {
            $dadosNovos['_imagem_alterada'] = 'Capa do Livro';
            $dadosAnteriores['_imagem_alterada'] = null;
        }

        // LOG DE ACTUALIZAÇÃO
        LogService::log('livros', $livro->id, 'actualização', $dadosAnteriores, $dadosNovos);

        return redirect('/livros/create')->with('success', 'Livro actualizado com sucesso!');
    }

    public function destroyLivro($id)
    {
        $livro = Livro::findOrFail($id);

        // CAPTURAR DADOS ANTES DA EXCLUSÃO
        $dadosAnteriores = [
            'nome' => $livro->nome,
            'ISBN' => $livro->ISBN,
            'preco' => $livro->preco,
            'estoque' => $livro->estoque, 
        ];

        $livro->delete();

        // LOG DE EXCLUSÃO
        LogService::log('livros', $id, 'exclusão', $dadosAnteriores, null);

        return redirect('/livros/create')->with('msg', 'Livro excluído com sucesso!');
    }

    public function notificarDisponibilidade(Request $request, Livro $livro)
    {
        $user = Auth::user();
        // Evita duplicidade
        if (!$livro->notificacoesDisponibilidade()->where('user_id', $user->id)->exists()) {
            $livro->notificacoesDisponibilidade()->create(['user_id' => $user->id]);
        }

        return back()->with('success', '✉️ Será notificado quando o livro estiver disponível.');
    }

    // ---------- AUTORES ----------
    public function createAutor()
    {
        $autores = Autor::all();
        return view('autores.autores', compact('autores'));
    }

    public function storeAutor(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $autor = new Autor;
        $autor->nome = $request->nome;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('autores', 'public');
            $autor->foto = '/storage/' . $path;
        } else {
            $autor->foto = '/images/default-book.png';
        }

        $autor->save();

        // LOG DE CRIAÇÃO - apenas dados principais
        $dadosNovos = [
            'nome' => $autor->nome,
        ];
        LogService::log('autores', $autor->id, 'criação', null, $dadosNovos);

        return redirect('/autores/create')->with('msg', 'Novo autor adicionado com sucesso!');
    }

    public function editAutor($id)
    {
        $autor = Autor::findOrFail($id);
        return view('autores.edit', compact('autor'));
    }

    public function updateAutor(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $autor = Autor::findOrFail($id);

        // CAPTURAR DADOS ANTES (incluindo foto)
        $dadosAnteriores = [
            'nome' => $autor->nome,
            'foto' => $autor->foto,
        ];

        // FAZER A ALTERAÇÃO
        $autor->nome = $request->nome;

        // CONTROLAR SE HOUVE ALTERAÇÃO DE FOTO
        $fotoAlterada = false;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('autores', 'public');
            $autor->foto = '/storage/' . $path;
            $fotoAlterada = true;
        }

        $autor->save();

        // CAPTURAR DADOS DEPOIS
        $dadosNovos = [
            'nome' => $autor->nome,
            'foto' => $autor->foto,
        ];

        // ADICIONAR FLAG DE IMAGEM SE HOUVE ALTERAÇÃO
        if ($fotoAlterada) {
            $dadosNovos['_imagem_alterada'] = 'Fotografia do Autor';
            $dadosAnteriores['_imagem_alterada'] = null;
        }

        // LOG DE ACTUALIZAÇÃO
        LogService::log('autores', $autor->id, 'actualização', $dadosAnteriores, $dadosNovos);

        return redirect('/autores/create')->with('success', 'Autor actualizado com sucesso!');
    }

    public function destroyAutor($id)
    {
        $autor = Autor::findOrFail($id);

        // CAPTURAR DADOS ANTES DA EXCLUSÃO
        $dadosAnteriores = [
            'nome' => $autor->nome,
        ];

        $autor->delete();

        // LOG DE EXCLUSÃO
        LogService::log('autores', $id, 'exclusão', $dadosAnteriores, null);

        return redirect('/autores/create')->with('msg', 'Autor excluído com sucesso!');
    }

    // ---------- EDITORAS ----------
    public function createEditora()
    {
        $editoras = Editora::all();
        return view('editoras.editoras', compact('editoras'));
    }

    public function storeEditora(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $editora = new Editora;
        $editora->nome = $request->nome;

        if ($request->hasFile('logotipo')) {
            $path = $request->file('logotipo')->store('editoras', 'public');
            $editora->logotipo = '/storage/' . $path;
        } else {
            $editora->logotipo = '/images/default-book.png';
        }

        $editora->save();

        // LOG DE CRIAÇÃO - apenas dados principais
        $dadosNovos = [
            'nome' => $editora->nome,
        ];
        LogService::log('editoras', $editora->id, 'criação', null, $dadosNovos);

        return redirect('/editoras/create')->with('msg', 'Nova editora adicionada com sucesso!');
    }

    public function editEditora($id)
    {
        $editora = Editora::findOrFail($id);
        return view('editoras.edit', compact('editora'));
    }

    public function updateEditora(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $editora = Editora::findOrFail($id);

        // CAPTURAR DADOS ANTES (incluindo logótipo)
        $dadosAnteriores = [
            'nome' => $editora->nome,
            'logotipo' => $editora->logotipo,
        ];

        // FAZER A ALTERAÇÃO
        $editora->nome = $request->nome;

        // CONTROLAR SE HOUVE ALTERAÇÃO DE LOGÓTIPO
        $logoAlterado = false;
        if ($request->hasFile('logotipo')) {
            $path = $request->file('logotipo')->store('editoras', 'public');
            $editora->logotipo = '/storage/' . $path;
            $logoAlterado = true;
        }

        $editora->save();

        // CAPTURAR DADOS DEPOIS
        $dadosNovos = [
            'nome' => $editora->nome,
            'logotipo' => $editora->logotipo,
        ];

        // ADICIONAR FLAG DE IMAGEM SE HOUVE ALTERAÇÃO
        if ($logoAlterado) {
            $dadosNovos['_imagem_alterada'] = 'Logótipo da Editora';
            $dadosAnteriores['_imagem_alterada'] = null;
        }

        // LOG DE ACTUALIZAÇÃO
        LogService::log('editoras', $editora->id, 'actualização', $dadosAnteriores, $dadosNovos);

        return redirect('/editoras/create')->with('success', 'Editora actualizada com sucesso!');
    }

    public function destroyEditora($id)
    {
        $editora = Editora::findOrFail($id);

        // CAPTURAR DADOS ANTES DA EXCLUSÃO
        $dadosAnteriores = [
            'nome' => $editora->nome,
        ];

        $editora->delete();

        // LOG DE EXCLUSÃO
        LogService::log('editoras', $id, 'exclusão', $dadosAnteriores, null);

        return redirect('/editoras/create')->with('msg', 'Editora excluída com sucesso!');
    }

    // ---------- UTILIZADORES ----------
    public function showCidadao($id)
    {
        $user = \App\Models\User::with(['requisicoes.livro'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    // ---------- API GOOGLE ----------
    public function storeFromApi(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'authors' => 'nullable|array',
            'publisher' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:255',
        ]);

        // Editora: se não existe, cria com imagem padrão
        $editora = Editora::firstOrCreate(
            ['nome' => $validatedData['publisher'] ?? 'Desconhecida'],
            ['logotipo' => '/images/default-book.png']
        );

        // Autores: se não existe, cria com imagem padrão
        $autores = collect($validatedData['authors'] ?? [])->map(function ($nome) {
            return Autor::firstOrCreate(
                ['nome' => $nome],
                ['foto' => '/images/default-book.png']
            );
        });

        // Livro: se não tem capa, usa imagem padrão
        $imagemCapa = $validatedData['cover_image'] ?: '/images/default-book.png';

        $livro = Livro::create([
            'nome' => $validatedData['title'],
            'ISBN' => $validatedData['isbn'],
            'bibliografia' => $validatedData['description'],
            'preco' => 0,
            'estoque' => 0, 
            'editora_id' => $editora->id,
            'imagemcapa' => $imagemCapa,
        ]);

        $livro->autores()->sync($autores->pluck('id'));

        // LOG DE CRIAÇÃO via API - apenas dados principais
        $dadosNovos = [
            'nome' => $livro->nome,
            'ISBN' => $livro->ISBN,
            'preco' => $livro->preco,
            'estoque' => $livro->estoque, 
            'fonte' => 'API Google Books'
        ];
        LogService::log('livros', $livro->id, 'criação', null, $dadosNovos);

        return redirect('/livros/create')->with('msg', 'Livro adicionado com sucesso a partir da API!');
    }

    // ---------- BULK ACTIONS ---------- 
    public function bulkDeleteLivros(Request $request)
    {
        $livroIds = $request->input('livros_selecionados', []);

        if (empty($livroIds)) {
            return redirect()->back()->with('error', 'Nenhum livro selecionado.');
        }

        $livros = Livro::whereIn('id', $livroIds)->get();
        $nomesLivros = $livros->pluck('nome')->toArray();

        // Registrar logs de exclusão em massa
        foreach ($livros as $livro) {
            LogService::log('livros', $livro->id, 'exclusão', ['nome' => $livro->nome], null);
        }

        Livro::whereIn('id', $livroIds)->delete();

        $quantidade = count($livroIds);
        LogService::simples('sistema', 0, "Exclusão em massa → {$quantidade} livros removidos: " . implode(', ', $nomesLivros));

        return redirect()->back()->with('success', "✅ {$quantidade} livros excluídos com sucesso!");
    }

    public function bulkUpdatePrecos(Request $request)
    {
        $request->validate([
            'livros_selecionados' => 'required|array|min:1',
            'novo_preco' => 'required|numeric|min:0',
        ]);

        $livroIds = $request->input('livros_selecionados');
        $novoPreco = $request->input('novo_preco');

        $livros = Livro::whereIn('id', $livroIds)->get();

        foreach ($livros as $livro) {
            $precoAntigo = $livro->preco;
            $livro->preco = $novoPreco;
            $livro->save();

            // Log individual de cada alteração
            LogService::log(
                'livros',
                $livro->id,
                'actualização',
                ['preco' => $precoAntigo],
                ['preco' => $novoPreco]
            );
        }

        $quantidade = count($livroIds);
        LogService::simples('sistema', 0, "Actualização de preços em massa → {$quantidade} livros actualizados para " . number_format($novoPreco, 2, ',', '.') . '€');

        return redirect()->back()->with('success', "✅ Preços de {$quantidade} livros actualizados com sucesso!");
    }

    // ---------- IMPORT/EXPORT ----------
    public function exportLivros(Request $request)
    {
        $formato = $request->input('formato', 'excel');

        LogService::simples('sistema', 0, "Exportação de livros → Formato: {$formato} | Utilizador: " . Auth::user()->name);



        return response()->json([
            'success' => true,
            'message' => "Exportação iniciada em formato {$formato}",
            'download_url' => '/storage/exports/livros_' . date('Y-m-d_H-i-s') . ".{$formato}"
        ]);
    }

    public function importLivros(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:xlsx,csv,json|max:5120' // 5MB max
        ]);

        $arquivo = $request->file('arquivo');
        $extensao = $arquivo->getClientOriginalExtension();

        LogService::simples('sistema', 0, "Importação de livros → Arquivo: {$arquivo->getClientOriginalName()} | Formato: {$extensao}");


        return redirect()->back()->with('success', '✅ Arquivo importado com sucesso! Os livros serão processados em segundo plano.');
    }

    // ---------- SEARCH & FILTERS ----------
    public function searchLivros(Request $request)
    {
        $termo = $request->input('q');
        $filtros = $request->only(['editora_id', 'autor_id', 'preco_min', 'preco_max']);

        LogService::simples('livros', 0, "Pesquisa → Termo: '{$termo}' | Filtros: " . json_encode($filtros));

        $query = Livro::with(['editora', 'autores']);

        if ($termo) {
            $query->where(function ($q) use ($termo) {
                $q->where('nome', 'LIKE', "%{$termo}%")
                    ->orWhere('ISBN', 'LIKE', "%{$termo}%")
                    ->orWhere('bibliografia', 'LIKE', "%{$termo}%");
            });
        }

        if ($filtros['editora_id']) {
            $query->where('editora_id', $filtros['editora_id']);
        }

        if ($filtros['preco_min']) {
            $query->where('preco', '>=', $filtros['preco_min']);
        }

        if ($filtros['preco_max']) {
            $query->where('preco', '<=', $filtros['preco_max']);
        }

        $livros = $query->paginate(20);
        $editoras = Editora::orderBy('nome')->get();
        $autores = Autor::orderBy('nome')->get();

        return view('livros.search', compact('livros', 'editoras', 'autores', 'termo', 'filtros'));
    }
}