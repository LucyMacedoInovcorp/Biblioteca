<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliController;

Route::get('/', function () {
    return view('welcome');
});


// Livros
Route::get('/livros/create', [BibliController::class, 'createLivro']);
//Formulário de novo livro
Route::post('/livros', [BibliController::class, 'storeLivro']);

// Autores
Route::get('/autores/create', [BibliController::class, 'createAutor']);
//Formulário de novo livro
Route::post('/autores', [BibliController::class, 'storeAutor']);

// Editoras
Route::get('/editoras/create', [BibliController::class, 'createEditora']);
Route::post('/editoras', [BibliController::class, 'storeEditora']);



// Dashboard Jetstream
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
