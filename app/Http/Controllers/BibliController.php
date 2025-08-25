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


    public function createLivro()
    {
        $livros = Livro::all();
        $editoras = Editora::orderBy('nome')->get();
        $livros = Livro::with(['editora', 'autores'])->get();

        $editoras = Editora::all();
        $autores = Autor::all();
        return view('livros.livros', compact('livros', 'editoras', 'autores'));
    }

    public function createAutor()
    {
        $autores = Autor::all();
        return view('autores.autores', ['autores' => $autores]);
    }

    public function createEditora()
    {
        $editoras = Editora::all();
        return view('editoras.editoras', ['editoras' => $editoras]);
    }


    //Requisição do formulário para inserir novo livro
    public function storeLivro(Request $request)
    {
        $livro = new Livro;
        $livro->nome         = $request->nome;
        $livro->ISBN         = $request->ISBN;
        $livro->bibliografia = $request->bibliografia;
        $livro->preco        = $request->preco;
        $livro->editora_id   = $request->editora_id;

        if ($request->hasFile('imagemcapa')) {
            $path = $request->file('imagemcapa')->store('images', 'public');
            $livro->imagemcapa = '/storage/' . $path;
        }

        $livro->save();

        if ($request->filled('autores')) {
            $livro->autores()->sync($request->autores);
        }

        return redirect('/livros/create')->with('msg', 'Novo livro adicionado com sucesso!');
    }

    //Requisição do formulário para inserir novo Autor
    public function storeAutor(Request $request)
    {
        $autor = new Autor;
        $autor->nome = $request->nome;

        if ($request->hasFile('foto')) {

            $path = $request->file('foto')->store('images', 'public');
            $autor->foto = '/storage/' . $path;
        }

        $autor->save();

        return redirect('/autores/create')->with('msg', 'Novo autor adicionado com sucesso!');
    }

    //Requisição do formulário para inserir nova editora
    public function storeEditora(Request $request)
    {
        $editora = new Editora;
        $editora->nome = $request->nome;

        if ($request->hasFile('logotipo')) {
            $path = $request->file('logotipo')->store('images', 'public');
            $editora->logotipo = '/storage/' . $path;
        }

        $editora->save();

        return redirect('/editoras/create')->with('msg', 'Nova editora adicionada com sucesso!');
    }

    //DELETE
    public function destroyLivro($id){
        Livro::findOrfail($id)->delete();
        return redirect('/livros/create')->with('msg', 'Livro excluído com sucesso!');
    }

        public function destroyEditora($id){
        Editora::findOrfail($id)->delete();
        return redirect('/editoras/create')->with('msg', 'Editora excluída com sucesso!');
    }
            public function destroyAutor($id){
        Autor::findOrfail($id)->delete();
        return redirect('/autores/create')->with('msg', 'Autor excluído com sucesso!');
    }

}


