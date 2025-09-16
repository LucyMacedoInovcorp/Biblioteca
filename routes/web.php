<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequisicaoController;
//GOOGLE API - ADMINISTRADOR
use App\Http\Controllers\BookSearchController;
//MAIL TESTE
use Illuminate\Support\Facades\Mail;
//AVALIAÇÕES
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\ReviewController;
//CARRINHO
use App\Http\Controllers\CarrinhoController;
//CHECKOUT
use App\Http\Controllers\CheckoutController;


Route::get('/', function () {
    return view('welcome');
});

/* --------------------LIVROS--------------------*/
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


/* --------------------AVALIAÇÕES--------------------*/
Route::get('/avaliacoes/create', [AvaliacaoController::class, 'create'])->name('avaliacoes.create');
// Salvar avaliação
Route::post('/avaliacoes', [AvaliacaoController::class, 'store'])->name('avaliacoes.store');

// Rotas para avaliações suspensas
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/avaliacoes/suspensas', [AvaliacaoController::class, 'pendentes'])->name('avaliacoes.suspensas');
    Route::post('/avaliacoes/suspensas/{id}/aprovar', [AvaliacaoController::class, 'aprovar'])->name('avaliacoes.suspensas.aprovar');
    Route::post('/avaliacoes/suspensas/{id}/recusar', [AvaliacaoController::class, 'rejeitar'])->name('avaliacoes.suspensas.recusar');
});


/* --------------------AVALIAÇÕES--------------------*/
Route::post('/livros/{livro}/notificar-disponibilidade', [BibliController::class, 'notificarDisponibilidade'])
    ->name('livros.notificar-disponibilidade')->middleware('auth');

/* --------------------CARRINHO--------------------*/
//Adicionar ao carrinho
Route::post('/carrinho/adicionar/{livro}', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
//Listar itens do carrinho
Route::get('/carrinho', [CarrinhoController::class, 'listar'])->name('carrinho.listar');
//Remover item do carrinho
Route::delete('/carrinho/remover/{item}', [CarrinhoController::class, 'remover'])->name('carrinho.remover');
//Atualizar quantidade do item no carrinho
Route::put('/carrinho/atualizar/{item}', [CarrinhoController::class, 'atualizar'])->name('carrinho.atualizar');

/* --------------------CHECKOUT--------------------*/
//Exibir a página de checkout
Route::get('/checkout', [CheckoutController::class, 'form'])->name('checkout');
//Processar o checkout - Formulário
Route::post('/checkout', [CheckoutController::class, 'finalizar'])->name('checkout.finalizar');



/*--------------------PEDIDOS--------------------*/
//Página de pedidos para admin
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/pedidos', [App\Http\Controllers\EncomendaController::class, 'todosPedidos'])->name('encomendas.todos');
});
// Página de pedidos do usuário autenticado
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/meus-pedidos', [App\Http\Controllers\EncomendaController::class, 'meusPedidos'])->name('encomendas.meus');
});

/* --------------------CHECKOUT STRIPE--------------------*/
    Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/encomendas/{encomenda}/pagar', [App\Http\Controllers\CheckoutStripeController::class, 'pagar'])->name('encomendas.pagar');
    Route::get('/checkout/success', [App\Http\Controllers\CheckoutStripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [App\Http\Controllers\CheckoutStripeController::class, 'cancel'])->name('checkout.cancel');
});