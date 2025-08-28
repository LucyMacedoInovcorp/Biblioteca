<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;

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

    //Para exibir detalhes do livro e suas requisições
    public function showLivro($id)
    {
        $livro = Livro::with(['requisicoes.user'])->findOrFail($id);

        return view('livros.show', compact('livro'));
    }

    public function storeLivro(Request $request)
    {
        $livro = new Livro;
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

        return redirect('/livros/create')->with('success', 'Livro atualizado com sucesso!');
    }

    public function destroyLivro($id)
    {
        Livro::findOrFail($id)->delete();
        return redirect('/livros/create')->with('msg', 'Livro excluído com sucesso!');
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
        }

        $autor->save();

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

        return redirect('/autores/create')->with('success', 'Autor atualizado com sucesso!');
    }

    public function destroyAutor($id)
    {
        Autor::findOrFail($id)->delete();
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
        }

        $editora->save();

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

        return redirect('/editoras/create')->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroyEditora($id)
    {
        Editora::findOrFail($id)->delete();
        return redirect('/editoras/create')->with('msg', 'Editora excluída com sucesso!');
    }

    //Detalhes do Cidadão
    public function showCidadao($id)
    {
        $user = \App\Models\User::with(['requisicoes.livro'])->findOrFail($id);

        return view('users.show', compact('user'));
    }
}
