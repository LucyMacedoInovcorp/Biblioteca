<?php

use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);
describe('RequisicaoController', function () {


    
    it('pode criar uma requisição de livro com sucesso', function () {
        Mail::fake();

        // Criar utilizador e livro na base de dados
        $user = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => true]);

        // Simular autenticação do utilizador
        $this->actingAs($user);

        // Simular a submissão de uma requisição (rota correta)
        $response = $this->post("/livros/{$livro->id}/requisitar");

        // Verificar que foi redirecionado com sucesso
        $response->assertRedirect('/livros/create');
        $response->assertSessionHas('success', '✅ Requisição realizada com sucesso! Um e-mail de confirmação foi enviado.');

        // Garantir que a requisição foi criada (usando 1 para MySQL boolean)
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



    it('não permite requisição sem autenticação', function () {
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
    it('não permite mais de 3 requisições ativas por utilizador', function () {
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
    it('não permite requisitar livro já requisitado por outro utilizador', function () {
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




    it('pode devolver um livro corretamente', function () {
        $user = User::factory()->create();
        $livro = Livro::factory()->create(['disponivel' => false]);

        // Criar requisição ativa
        $requisicao = Requisicao::factory()->create([
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'ativo' => 1, // MySQL boolean como 1
            'data_recepcao' => null,
            'dias_decorridos' => null,
            'created_at' => now()->subDays(10), // Criada há 10 dias
        ]);

        $this->actingAs($user);

        // Simular devolução (rota correta)
        $response = $this->post("/requisicoes/{$requisicao->id}/confirmar");

        $response->assertRedirect();
        $response->assertSessionHas('success', '✅ Devolução confirmada!');

        // Verificar que o estado da requisição foi atualizado
        $requisicao->refresh();
        $livro->refresh();

        expect($requisicao->ativo)->toBe(0); // MySQL retorna 0 em vez de false
        expect($requisicao->data_recepcao)->not->toBeNull();
        // Usar toBeTrue() em vez de toBe(1) para maior flexibilidade
        expect($livro->disponivel)->toBeTrue(); // Aceita tanto true quanto 1

        // Verificar na base de dados
        $this->assertDatabaseHas('requisicoes', [
            'id' => $requisicao->id,
            'ativo' => 0, // MySQL boolean como 0
        ]);

        $this->assertDatabaseHas('livros', [
            'id' => $livro->id,
            'disponivel' => 1, // MySQL boolean como 1
        ]);
    });






    it('pode listar requisições por utilizador corretamente', function () {
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

        // Verificar que o utilizador vê apenas suas requisições
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

        // Admin deve ver todas as 5 requisições
        expect($requisicoes)->toHaveCount(5);
    });
    it('valida que requisição não pode ser criada sem livro válido', function () {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Tentar criar requisição com ID de livro inexistente
        $response = $this->post('/livros/999/requisitar');

        // Deve retornar 404 pois o livro não existe
        $response->assertStatus(404);

        // Verificar que nenhuma requisição foi criada
        $this->assertDatabaseCount('requisicoes', 0);
    });
    it('calcula dias decorridos corretamente na devolução', function () {
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

        // Fazer devolução
        $this->post("/requisicoes/{$requisicao->id}/confirmar");

        $requisicao->refresh();

        // Verificar que os dias foram calculados corretamente (pode ter 1 dia de diferença devido ao timing)
        expect($requisicao->dias_decorridos)->toBeGreaterThanOrEqual($diasAnteriores - 1);
        expect($requisicao->dias_decorridos)->toBeLessThanOrEqual($diasAnteriores + 1);
    });
    it('não permite utilizador exceder limite de 3 requisições ativas', function () {
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
    it('permite requisitar após devolver um livro', function () {
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
