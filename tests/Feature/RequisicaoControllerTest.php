<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

describe('RequisicaoController', function () {
    
    it('pode criar uma requisição de livro com sucesso', function () {
        // Criar usuário e livro na base de dados
        $user = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => true]);
        
        // Simular autenticação do usuário
        $this->actingAs($user);
        
        // Simular submissão de requisição
        $response = $this->post("/requisicoes/{$livro->id}");
        
        // Verificar redirecionamento com sucesso
        $response->assertRedirect('/livros/create');
        $response->assertSessionHas('success', '✅ Requisição realizada com sucesso! Um e-mail de confirmação foi enviado.');
        
        // Garantir que a requisição foi criada
        $this->assertDatabaseHas('requisicoes', [
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'ativo' => true,
        ]);
        
        // Verificar se existe apenas uma requisição
        expect(Requisicao::count())->toBe(1);
        
        // Verificar se os dados da requisição estão corretos
        $requisicao = Requisicao::first();
        expect($requisicao->user_id)->toBe($user->id);
        expect($requisicao->livro_id)->toBe($livro->id);
        expect($requisicao->ativo)->toBe(true);
    });
    
    it('não permite requisição sem autenticação', function () {
        $livro = Livro::factory()->create();
        
        $response = $this->post("/requisicoes/{$livro->id}");
        
        $response->assertRedirect('/livros/create');
        $response->assertSessionHasErrors(['error' => '❌ Você precisa estar autenticado para requisitar um livro.']);
        
        $this->assertDatabaseCount('requisicoes', 0);
    });
    
    it('não permite mais de 3 requisições ativas', function () {
        $user = User::factory()->create();
        
        // Criar 3 requisições ativas
        Requisicao::factory()->count(3)->create([
            'user_id' => $user->id,
            'ativo' => true,
        ]);
        
        $livro = Livro::factory()->create();
        $this->actingAs($user);
        
        $response = $this->post("/requisicoes/{$livro->id}");
        
        $response->assertRedirect('/livros/create');
        $response->assertSessionHasErrors(['error' => '❌ Você já tem 3 livros ativos requisitados.']);
    });
    
    it('não permite requisitar livro já requisitado', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $livro = Livro::factory()->create();
        
        // Primeiro usuário requisita o livro
        Requisicao::factory()->create([
            'user_id' => $user1->id,
            'livro_id' => $livro->id,
            'ativo' => true,
        ]);
        
        // Segundo usuário tenta requisitar o mesmo livro
        $this->actingAs($user2);
        $response = $this->post("/requisicoes/{$livro->id}");
        
        $response->assertRedirect('/livros/create');
        $response->assertSessionHasErrors(['error' => '❌ Este livro já está requisitado por outro usuário.']);
    });
    
    it('envia emails de confirmação', function () {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $livro = Livro::factory()->create();
        
        $this->actingAs($user);
        $this->post("/requisicoes/{$livro->id}");
        
        Mail::assertSent(\App\Mail\NovaRequisicaoMail::class, 2); // Para usuário e admin
    });
    
    it('pode confirmar devolução de livro', function () {
        $user = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => false]);
        $requisicao = Requisicao::factory()->create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'ativo' => true,
        ]);
        
        $this->actingAs($user);
        $response = $this->patch("/requisicoes/{$requisicao->id}/confirmar-rececao");
        
        $response->assertRedirect();
        $response->assertSessionHas('success', '✅ Devolução confirmada!');
        
        $requisicao->refresh();
        $livro->refresh();
        
        expect($requisicao->ativo)->toBe(false);
        expect($requisicao->data_recepcao)->not->toBeNull();
        expect($requisicao->dias_decorridos)->toBeGreaterThan(0);
        expect($livro->disponivel)->toBe(true);
    });
    
});