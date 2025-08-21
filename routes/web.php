<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliController;

Route::get('/', function () {
    return view('welcome');
});


// Livros
Route::get('/livros/create', [BibliController::class, 'createLivro']);
//Formulário de novo livro
Route::post('/livros', [BibliController::class, 'store']);

// Autores
Route::get('/autores/create', [BibliController::class, 'createAutor']);

// Editoras
Route::get('/editoras/create', [BibliController::class, 'createEditora']);



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
