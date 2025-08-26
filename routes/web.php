<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliController;
use App\Http\Controllers\UserController;



Route::get('/', function () {
    return view('welcome');
});

/* --------------------LIVROS--------------------*/
//CREATE - Exibe o formulário de criação de um novo livro
Route::get('/livros/create', [BibliController::class, 'createLivro']);
//CREATE - Exibe o formulário de criação de um novo livro
Route::post('/livros', [BibliController::class, 'storeLivro']);
//EDIT - Exibe o formulário de alteração de um livro
Route::get('/livros/{id}/edit', [BibliController::class, 'editLivro'])->name('livros.edit');
//EDIT - Atualiza um registo de um livro
Route::put('/livros/{id}', [BibliController::class, 'updateLivro'])->name('livros.update');
//DELETE - Rota para excluir um registo de um livro
Route::delete('/livros/{id}', [BibliController::class, 'destroyLivro'])->name('livros.destroy');



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

