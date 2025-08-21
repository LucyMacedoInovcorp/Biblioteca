<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro; 

class BibliController extends Controller
{
public function indexLivros()
{
    return view('welcome');
}


    public function createLivro()
    {
        $livros = Livro::all();
        return view('livros.livros', ['livros' => $livros]);
    }

    public function createAutor()
    {
        return view('autores.autores');
    }

    public function createEditora()
    {
        return view('editoras.editoras');
    }

    //Requisição do formulário para inserir novo livro
    public function store(Request $request){
        $livros = new Livro;
        $livros->nome = $request->nome;
        $livros->ISBN = $request->ISBN;
        $livros->bibliografia = $request->bibliografia;
        $livros->preco = $request->preco;

        $livros->save();

        return redirect('/livros/create');

    }

}
