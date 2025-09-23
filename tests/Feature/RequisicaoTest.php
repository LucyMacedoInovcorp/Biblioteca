<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);
describe('RequisicaoController', function () {


    /*-----------------1. Teste de Criação de Requisição de Livro-----------------*/    
    it('1. Teste de Criação de Requisição de Livro', function () {
        Mail::fake();

        // 1.1 ---> Criar um utilizador e um livro na base de dados.
        $user = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => true]);

        // Simular autenticação do utilizador
        $this->actingAs($user);

        // 1.2 ---> Simular a submissão de uma requisição.
        $response = $this->post("/livros/{$livro->id}/requisitar");

        // Verificar que foi redirecionado com sucesso
        $response->assertRedirect('/livros/create');
        $response->assertSessionHas('success', '✅ Requisição realizada com sucesso! Um e-mail de confirmação foi enviado.');

        // 1.3 ---> Garantir que a requisição foi criada e que os dados estão corretos.
        $this->assertDatabaseHas('requisicoes', [
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'ativo' => 1, // MySQL boolean como 1
        ]);

        // ADICIONAR verificação de que existe requisição ativa (isso torna o livro "indisponível")
        $this->assertTrue($livro->fresh()->requisicoes()->where('ativo', 1)->exists());

        // Garantir que os dados estão corretos
        $requisicao = Requisicao::where('user_id', $user->id)
            ->where('livro_id', $livro->id)
            ->first();

        expect($requisicao)->not->toBeNull();
        expect($requisicao->ativo)->toBe(1); // MySQL retorna 1 em vez de true
        expect($requisicao->user_id)->toBe($user->id);
        expect($requisicao->livro_id)->toBe($livro->id);
    });

        /*-----------------2. Teste de Validação de Requisição-----------------*/
    it('2. Teste de Validação de Requisição', function () {

        // 2.1 ---> Simular uma requisição sem um livro válido.
        $user = User::factory()->create();
        $this->actingAs($user);

        // Tentar criar requisição com ID de livro inexistente
        $response = $this->post('/livros/999/requisitar');

        // 2.2 ---> Verificar se o Laravel retorna erro de validação adequado.
        $response->assertStatus(404);

        // Verificar que nenhuma requisição foi criada
        $this->assertDatabaseCount('requisicoes', 0);
    });

        /*-----------------3. Teste de Devolução de Livro-----------------*/  
    it('3. Teste de Devolução de Livro', function () {
        $user = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => false]);

        // 3.1 ---> Criar uma requisição ativa na base de dados.
        $requisicao = Requisicao::factory()->create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'ativo' => 1, 
            'data_recepcao' => null,
            'dias_decorridos' => null,
            'created_at' => now()->subDays(10), // Criada há 10 dias
        ]);

        $this->actingAs($user);

        // Simular devolução (rota correta)
        $response = $this->post("/requisicoes/{$requisicao->id}/confirmar");

        $response->assertRedirect();
        $response->assertSessionHas('success', '✅ Devolução confirmada!');

        // 3.3 ---> Verificar se a requisição foi atualizada corretamente.
        $requisicao->refresh();
        $livro->refresh();

        expect($requisicao->ativo)->toBe(0); 
        expect($requisicao->data_recepcao)->not->toBeNull();
        expect($livro->disponivel)->toBeTrue(); 

        // Verificar na base de dados
        $this->assertDatabaseHas('requisicoes', [
            'id' => $requisicao->id,
            'ativo' => 0, 
        ]);

        $this->assertDatabaseHas('livros', [
            'id' => $livro->id,
            'disponivel' => 1, 
        ]);
    });

        /*-----------------3. Teste de Devolução de Livro (Parte 2 - Devolução)-----------------*/
    it('3. Teste de Devolução de Livro (Parte 2 - Devolução)', function () {
        $user = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => false]);

        $diasAnteriores = 15;

        // Criar requisição com data específica
        $requisicao = Requisicao::factory()->create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'ativo' => 1,
            'created_at' => now()->subDays($diasAnteriores),
        ]);

        $this->actingAs($user);

        // 3.2 ---> Simular uma requisição para devolver o livro.
        $this->post("/requisicoes/{$requisicao->id}/confirmar");

        $requisicao->refresh();

        // Verificar que os dias foram calculados corretamente (pode ter 1 dia de diferença devido ao timing)
        expect($requisicao->dias_decorridos)->toBeGreaterThanOrEqual($diasAnteriores - 1);
        expect($requisicao->dias_decorridos)->toBeLessThanOrEqual($diasAnteriores + 1);
    });

        /*-----------------4. Teste de Listagem de Requisições por Utilizador-----------------*/
    it('4. Teste de Listagem de Requisições por Utilizador', function () {


        // 4.1 ---> Criar múltiplas requisições para diferentes utilizadores.
        // Criar utilizadores
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        // Criar requisições para user1
        Requisicao::factory()->count(3)->create([
            'user_id' => $user1->id,
        ]);

        // Criar requisições para user2
        Requisicao::factory()->count(2)->create([
            'user_id' => $user2->id,
        ]);

        // Testar como utilizador normal - deve ver apenas suas requisições
        $this->actingAs($user1);
        $response = $this->get('/requisicoes');

        $response->assertStatus(200);
        $response->assertViewIs('requisicoes.index');

        // 4.2 ---> Simular um pedido para obter as requisições de um utilizador específico.
        $requisicoes = $response->viewData('requisicoes');
        expect($requisicoes)->toHaveCount(3);

        foreach ($requisicoes as $requisicao) {
            expect($requisicao->user_id)->toBe($user1->id);
        }

        // Testar como admin - deve ver todas as requisições
        $this->actingAs($admin);
        $response = $this->get('/requisicoes');

        $response->assertStatus(200);
        $requisicoes = $response->viewData('requisicoes');

        // 4.3 ---> Verificar se apenas as requisições corretas são retornadas.
        expect($requisicoes)->toHaveCount(5);
    });

        /*-----------------Testes Complementares-----------------*/
    
    it('Teste complementar - Não permite requisição sem autenticação', function () {
        $livro = Livro::factory()->create(['disponivel' => true]);

        // Tentar fazer requisição sem estar autenticado
        $response = $this->post("/livros/{$livro->id}/requisitar");

        // Deve ser redirecionado para login
        $response->assertRedirect('/login');

        // Verificar que nenhuma requisição foi criada
        $this->assertDatabaseCount('requisicoes', 0);

        // Verificar que o livro continuou disponível
        $this->assertDatabaseHas('livros', [
            'id' => $livro->id,
            'disponivel' => 1, // MySQL boolean como 1
        ]);
    });

    it('Teste complementar - Não permite mais de 3 requisições ativas por utilizador', function () {
        Mail::fake();

        $user = User::factory()->create();
        $livros = Livro::factory()->count(4)->create(['disponivel' => true]);

        // Criar 3 requisições ativas
        for ($i = 0; $i < 3; $i++) {
            Requisicao::factory()->create([
                'user_id' => $user->id,
                'livro_id' => $livros[$i]->id,
                'ativo' => 1, // MySQL boolean como 1
            ]);
        }

        $this->actingAs($user);

        // Tentar criar a 4ª requisição
        $response = $this->post("/livros/{$livros[3]->id}/requisitar");

        $response->assertRedirect('/livros/create');
        $response->assertSessionHasErrors(['error' => '❌ Você já tem 3 livros ativos requisitados.']);

        // Verificar que a 4ª requisição não foi criada
        $this->assertDatabaseMissing('requisicoes', [
            'user_id' => $user->id,
            'livro_id' => $livros[3]->id,
        ]);

        // Verificar que ainda tem apenas 3 requisições ativas (usando 1 para MySQL)
        expect($user->requisicoes()->where('ativo', 1)->count())->toBe(3);
    });

    it('Teste complementar - Não permite requisitar livro já requisitado por outro utilizador', function () {
        Mail::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => false]);

        // Criar requisição ativa do user1
        Requisicao::factory()->create([
            'user_id' => $user1->id,
            'livro_id' => $livro->id,
            'ativo' => 1, // MySQL boolean como 1
        ]);

        // User2 tenta requisitar o mesmo livro
        $this->actingAs($user2);
        $response = $this->post("/livros/{$livro->id}/requisitar");

        $response->assertRedirect('/livros/create');
        $response->assertSessionHasErrors(['error' => '❌ Este livro já está requisitado por outro usuário.']);

        // Verificar que não foi criada requisição para user2
        $this->assertDatabaseMissing('requisicoes', [
            'user_id' => $user2->id,
            'livro_id' => $livro->id,
        ]);

        // Verificar que só existe 1 requisição para este livro
        expect(Requisicao::where('livro_id', $livro->id)->count())->toBe(1);
    });


    it('Teste complementar - Não permite utilizador exceder limite de 3 requisições ativas', function () {
        Mail::fake();

        $user = User::factory()->create();

        // Criar exatamente 3 requisições ativas (limite máximo)
        $livros = Livro::factory()->count(4)->create(['disponivel' => true]);

        for ($i = 0; $i < 3; $i++) {
            Requisicao::factory()->create([
                'user_id' => $user->id,
                'livro_id' => $livros[$i]->id,
                'ativo' => 1,
            ]);
        }

        $this->actingAs($user);

        // Verificar que o utilizador tem 3 requisições ativas
        expect($user->requisicoes()->where('ativo', 1)->count())->toBe(3);

        // Tentar requisitar o 4º livro (deve falhar)
        $response = $this->post("/livros/{$livros[3]->id}/requisitar");

        $response->assertRedirect('/livros/create');
        $response->assertSessionHasErrors(['error' => '❌ Você já tem 3 livros ativos requisitados.']);

        // Verificar que ainda tem apenas 3 requisições
        expect($user->fresh()->requisicoes()->where('ativo', 1)->count())->toBe(3);
    });

    it('Teste complementar - Permite requisitar após devolver um livro', function () {
        Mail::fake();

        $user = User::factory()->create();
        $livros = Livro::factory()->count(4)->create(['disponivel' => true]);

        // Criar 3 requisições ativas (limite máximo)
        $requisicoes = [];
        for ($i = 0; $i < 3; $i++) {
            $requisicoes[] = Requisicao::factory()->create([
                'user_id' => $user->id,
                'livro_id' => $livros[$i]->id,
                'ativo' => 1,
            ]);
        }

        $this->actingAs($user);

        // Devolver um livro
        $this->post("/requisicoes/{$requisicoes[0]->id}/confirmar");

        // Agora deve conseguir requisitar outro livro
        $response = $this->post("/livros/{$livros[3]->id}/requisitar");

        $response->assertRedirect('/livros/create');
        $response->assertSessionHas('success');

        // Verificar que a nova requisição foi criada
        $this->assertDatabaseHas('requisicoes', [
            'user_id' => $user->id,
            'livro_id' => $livros[3]->id,
            'ativo' => 1,
        ]);

        // Verificar que tem 3 requisições ativas (2 antigas + 1 nova)
        expect($user->fresh()->requisicoes()->where('ativo', 1)->count())->toBe(3);
    });
});
