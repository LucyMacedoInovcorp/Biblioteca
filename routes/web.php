<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequisicaoController;
//GOOGLE API - ADMINISTRADOR
use App\Http\Controllers\BookSearchController;
//MAIL TESTE
use Illuminate\Support\Facades\Mail;


Route::get('/', function () {
    return view('welcome');
});

/* --------------------LIVROS--------------------*/
// ----------------- LIVROS -----------------
// CREATE
Route::get('/livros/create', [BibliController::class, 'createLivro']);
Route::post('/livros', [BibliController::class, 'storeLivro']);

// Busca de livros (deve vir antes da rota dinâmica /livros/{id})
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/livros/search', [BibliController::class, 'searchLivros'])->name('livros.search');
});

// EDIT
Route::get('/livros/{id}/edit', [BibliController::class, 'editLivro'])->name('livros.edit');
Route::put('/livros/{id}', [BibliController::class, 'updateLivro'])->name('livros.update');

// DELETE
Route::delete('/livros/{id}', [BibliController::class, 'destroyLivro'])->name('livros.destroy');

// SHOW - detalhe do livro
Route::get('/livros/{id}', [BibliController::class, 'showLivro'])->name('livros.show');




/* --------------------AUTORES--------------------*/
//CREATE - Exibe o formulário de criação de um novo autor
Route::get('/autores/create', [BibliController::class, 'createAutor']);
//CREATE - Exibe o formulário de criação de um novo autor
Route::post('/autores', [BibliController::class, 'storeAutor']);
//EDIT - Exibe o formulário de alteração de um autor
Route::get('/autores/{id}/edit', [BibliController::class, 'editAutor'])->name('autores.edit');
//EDIT - Atualiza um registo de um autor
Route::put('/autores/{id}', [BibliController::class, 'updateAutor'])->name('autores.update');
//DELETE - Rota para excluir um registo de um autor
Route::delete('/autores/{id}', [BibliController::class, 'destroyAutor'])->name('autores.destroy');


/* --------------------EDITORAS--------------------*/
//CREATE - Exibe o formulário de criação de uma nova editora
Route::get('/editoras/create', [BibliController::class, 'createEditora']);
//CREATE - Exibe o formulário de criação de uma nova editora
Route::post('/editoras', [BibliController::class, 'storeEditora']);
//EDIT - Exibe o formulário de alteração de uma editora
Route::get('/editoras/{id}/edit', [BibliController::class, 'editEditora'])->name('editoras.edit');
//EDIT - Atualiza um registo de uma editora
Route::put('/editoras/{id}', [BibliController::class, 'updateEditora'])->name('editoras.update');
//DELETE - Rota para excluir um registo de uma editora
Route::delete('/editoras/{id}', [BibliController::class, 'destroyEditora'])->name('editoras.destroy');


/* --------------------AUTENTICAÇÃO--------------------*/
// DASHBOARD (Define as rotas que só podem ser acessadas por utilizadores autenticados/cidadãos logados)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


// DASHBOARD (Define as rotas que só podem ser acessadas por utilizadores autenticados/administradores)
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });

/* --------------------CRIAR USERS (ADMINISTRADOR)--------------------*/
Route::middleware(['auth', 'verified', 'admin']) 
    ->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });


/* --------------------REQUISIÇÃO--------------------*/
Route::post('/livros/{livro}/requisitar', [RequisicaoController::class, 'store'])
    ->middleware('auth')
    ->name('livros.requisitar');

Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');

Route::post('/requisicoes/{id}/confirmar', [RequisicaoController::class, 'confirmarRececao'])
    ->name('requisicoes.confirmar');

/* --------------------DETALHE DOS CIDADÃOS--------------------*/
  Route::get('/users/{id}', [BibliController::class, 'showCidadao'])->name('users.show');

  /* --------------------API GOOGLE--------------------*/
// Rota para a página de pesquisa
Route::get('/books/search', [BookSearchController::class, 'index'])->name('books.search.index');

// Rota para processar a pesquisa
Route::get('/books/search-results', [BookSearchController::class, 'search'])->name('books.search.results');

// Salvar livro vindo da API Google Books
Route::post('/books/store-from-api', [BibliController::class, 'storeFromApi'])
    ->name('books.storeFromApi');





 