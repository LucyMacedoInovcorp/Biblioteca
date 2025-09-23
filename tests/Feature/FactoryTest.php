<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\Editora;

beforeEach(function () {
    $this->artisan('migrate:fresh');
});

describe('Factories', function () {
    it('pode criar um usuário', function () {
        $user = User::factory()->create();
        
        expect($user)->toBeInstanceOf(User::class);
        expect($user->name)->not->toBeNull();
        expect($user->email)->not->toBeNull();
    });

    it('pode criar uma editora', function () {
        $editora = Editora::factory()->create();
        
        expect($editora)->toBeInstanceOf(Editora::class);
        expect($editora->nome)->not->toBeNull();
    });

    it('pode criar um livro', function () {
        $livro = Livro::factory()->create();
        
        expect($livro)->toBeInstanceOf(Livro::class);
        expect($livro->nome)->not->toBeNull();
        expect($livro->disponivel)->toBe(true);
    });

    it('pode criar uma requisição', function () {
        $requisicao = Requisicao::factory()->create();
        
        expect($requisicao)->toBeInstanceOf(Requisicao::class);
        expect($requisicao->user_id)->not->toBeNull();
        expect($requisicao->livro_id)->not->toBeNull();
        expect($requisicao->ativo)->toBe(true);
    });
});