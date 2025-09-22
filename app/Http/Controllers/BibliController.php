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
        $relacionados = $livro->relacionados(); // <-- Adicione esta linha

        return view('livros.show', compact('livro', 'relacionados'));
    }

    public function storeLivro(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'ISBN' => 'nullable|string|max:255',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric',
            'editoras' => 'required|array|min:1',
            'autores' => 'required|array|min:1',
        ]);

        $livro = new Livro;
        $livro->nome         = $request->nome;
        $livro->ISBN         = $request->ISBN;
        $livro->bibliografia = $request->bibliografia;
        $livro->preco        = $request->preco;
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
        //LOG DE CRIAÇÃO
        LogService::log('livros', $livro->id, 'criação', null, $livro->toArray());

        return redirect('/livros/create')->with('msg', 'Novo livro adicionado com sucesso!');
    }

    public function editLivro($id)
    {
        $livro    = Livro::with('autores')->findOrFail($id);
        $editoras = Editora::all();
        $autores  = Autor::all();

        return view('livros.edit', compact('livro', 'editoras', 'autores'));
    }

    //Lógica anterior de updateLivro (sem logs)
    public function updateLivro(Request $request, $id)
    {
        $livro = Livro::findOrFail($id);

        $livro->nome         = $request->nome;
        $livro->ISBN         = $request->ISBN;
        $livro->bibliografia = $request->bibliografia;
        $livro->preco        = $request->preco;
        $livro->editora_id   = $request->editora_id;

        if ($request->hasFile('imagemcapa')) {
            $path = $request->file('imagemcapa')->store('capas', 'public');
            $livro->imagemcapa = '/storage/' . $path;
        }

        $livro->save();

        if ($request->filled('autores')) {
            $livro->autores()->sync($request->autores);
        }


        //LOG DE EDIÇÃO
        $dadosAnteriores = $livro->getOriginal();
        LogService::log('livros', $livro->id, 'atualização', $dadosAnteriores, $livro->toArray());
        return redirect('/livros/create')->with('success', 'Livro atualizado com sucesso!');
    }
   



    public function destroyLivro($id)
    {
        Livro::findOrFail($id)->delete();

        //LOG DE EXCLUSÃO
        $dadosLivro = Livro::withTrashed()->find($id)->toArray();
        LogService::log('livros', $id, 'exclusão', $dadosLivro, null);
        return redirect('/livros/create')->with('msg', 'Livro excluído com sucesso!');
    }

    public function notificarDisponibilidade(Request $request, Livro $livro)
    {
        $user = Auth::user();
        // Evita duplicidade
        if (!$livro->notificacoesDisponibilidade()->where('user_id', $user->id)->exists()) {
            $livro->notificacoesDisponibilidade()->create(['user_id' => $user->id]);
        }

        return back()->with('success', '✉️ Você será notificado quando o livro estiver disponível.');

    }

    // ---------- AUTORES ----------
    public function createAutor()
    {
        $autores = Autor::all();
        return view('autores.autores', compact('autores'));
    }

    public function storeAutor(Request $request)
    {
        $autor = new Autor;
        $autor->nome = $request->nome;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('autores', 'public');
            $autor->foto = '/storage/' . $path;
        } else {
            // Caminho relativo à pasta public
            $autor->foto = '/images/default-book.png';
        }

        $autor->save();

        //LOG DE CRIAÇÃO
        LogService::log('autores', $autor->id, 'criação', null, $autor->toArray());
        return redirect('/autores/create')->with('msg', 'Novo autor adicionado com sucesso!');
    }

    public function editAutor($id)
    {
        $autor = Autor::findOrFail($id);
        return view('autores.edit', compact('autor'));
    }

    public function updateAutor(Request $request, $id)
    {
        $autor = Autor::findOrFail($id);
        $autor->nome = $request->nome;

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('autores', 'public');
            $autor->foto = '/storage/' . $path;
        }

        $autor->save();

        //LOG DE EDIÇÃO
        $dadosAnteriores = $autor->getOriginal();
        LogService::log('autores', $autor->id, 'atualização', $dadosAnteriores, $autor->toArray());
        return redirect('/autores/create')->with('success', 'Autor atualizado com sucesso!');
    }

    public function destroyAutor($id)
    {
        Autor::findOrFail($id)->delete();
        //LOG DE EXCLUSÃO
        $dadosAutor = Autor::withTrashed()->find($id)->toArray();
        LogService::log('autores', $id, 'exclusão', $dadosAutor, null);
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
        $editora = new Editora;
        $editora->nome = $request->nome;

        if ($request->hasFile('logotipo')) {
            $path = $request->file('logotipo')->store('editoras', 'public');
            $editora->logotipo = '/storage/' . $path;
        } else {
            // Caminho relativo à pasta public
            $editora->logotipo = '/images/default-book.png';
        }

        $editora->save();

        //LOG DE CRIAÇÃO
        LogService::log('editoras', $editora->id, 'criação', null, $editora->toArray());
        return redirect('/editoras/create')->with('msg', 'Nova editora adicionada com sucesso!');
    }

    public function editEditora($id)
    {
        $editora = Editora::findOrFail($id);
        return view('editoras.edit', compact('editora'));
    }

    public function updateEditora(Request $request, $id)
    {
        $editora = Editora::findOrFail($id);
        $editora->nome = $request->nome;

        if ($request->hasFile('logotipo')) {
            $path = $request->file('logotipo')->store('editoras', 'public');
            $editora->logotipo = '/storage/' . $path;
        }

        $editora->save();

        //LOG DE EDIÇÃO
        $dadosAnteriores = $editora->getOriginal();
        LogService::log('editoras', $editora->id, 'atualização', $dadosAnteriores, $editora->toArray());        return redirect('/editoras/create')->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroyEditora($id)
    {
        Editora::findOrFail($id)->delete();
        //LOG DE EXCLUSÃO
        $dadosEditora = Editora::withTrashed()->find($id)->toArray();
        LogService::log('editoras', $id, 'exclusão', $dadosEditora, null);
        return redirect('/editoras/create')->with('msg', 'Editora excluída com sucesso!');
    }

    //Detalhes do Cidadão
    public function showCidadao($id)
    {
        $user = \App\Models\User::with(['requisicoes.livro'])->findOrFail($id);

        return view('users.show', compact('user'));
    }

    // ---------- API GOOGLE ----------
    /**
     * Armazena um livro no banco de dados a partir dos dados da API do Google Books.
     * Este método é chamado pela rota 'books.store'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */



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
            'editora_id' => $editora->id,
            'imagemcapa' => $imagemCapa,
        ]);

        $livro->autores()->sync($autores->pluck('id'));

        //LOG DE CRIAÇÃO
        LogService::log('livros', $livro->id, 'criação', null, $livro->toArray());  
        
        return redirect('/livros/create')->with('msg', 'Livro adicionado com sucesso a partir da API!');
    }
}
