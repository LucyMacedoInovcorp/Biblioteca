<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BibliController extends Controller
{
    // ----------- LIVROS -----------
    public function createLivro()
    {
        return view('livros.livros');
    }

    // ----------- AUTORES -----------
    public function createAutor()
    {
        return view('autores.autores');
    }

    // ----------- EDITORAS -----------
    public function createEditora()
    {
        return view('editoras.editoras');
    }
}
