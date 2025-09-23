<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Teste Simples Requisicao', function () {
    it('factories funcionam corretamente', function () {
        $user = User::factory()->create();
        $livro = Livro::factory()->create();
        $requisicao = Requisicao::factory()->create();
        
        expect($user)->toBeInstanceOf(User::class);
        expect($livro)->toBeInstanceOf(Livro::class);
        expect($requisicao)->toBeInstanceOf(Requisicao::class);
    });
    
    it('pode criar uma requisição básica', function () {
        $user = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => true]);
        
        $this->actingAs($user);
        
        // Simular POST para criar requisição
        $response = $this->post("/requisicoes/{$livro->id}");
        
        // Só verificar que não deu erro 500
        expect($response->status())->not->toBe(500);
    });
});