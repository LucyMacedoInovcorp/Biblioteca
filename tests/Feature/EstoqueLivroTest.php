<?php

use App\Models\Livro;
use App\Models\User;


/*---------------------------5. Teste de Stock na Encomenda de Livros---------------------------*/
describe('5. Teste de Stock na Encomenda de Livros', function () {

    beforeEach(function () {
        // Criar um usuário para autenticação se necessário
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    it('5. Teste de Stock na Encomenda de Livros', function () {
        // 5.1 ---> Criar um livro com stock = 0.
        $livro = Livro::factory()->create([
            'estoque' => 0,
            'nome' => 'Livro Sem Estoque',
            'ISBN' => '1234567890',
            'preco' => 29.90
        ]);

        // 5.2 ---> Tentar criar uma requisição para esse livro.
        $response = $this->postJson('/requisicoes', [
            'livro_id' => $livro->id,
            'quantidade' => 1
        ]);

        // 5.3 ---> Verificar se a aplicação impede a operação com uma mensagem de erro.
        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'Livro sem estoque disponível'
                ]);
    });

    it('Teste complementar - É possível requisitar um livro com stock disponível', function () {
        // Criar um livro com stock disponível
        $livro = Livro::factory()->create([
            'estoque' => 5,
            'nome' => 'Livro Com Estoque',
            'ISBN' => '0987654321',
            'preco' => 39.90
        ]);

        // Criar uma requisição válida
        $response = $this->postJson('/requisicoes', [
            'livro_id' => $livro->id,
            'quantidade' => 1
        ]);

        $response->assertStatus(201); // Created

        // Verificar se o estoque foi reduzido
        $livro->refresh();
        expect($livro->estoque)->toBe(4);
    });

    it('Teste complementar - Não é possível requisitar mais livros do que o stock disponível', function () {
        // Criar um livro com stock limitado
        $livro = Livro::factory()->create([
            'estoque' => 2,
            'nome' => 'Livro Stock Limitado',
            'ISBN' => '1111111111',
            'preco' => 25.50
        ]);

        // Tentar requisitar mais do que o disponível
        $response = $this->postJson('/requisicoes', [
            'livro_id' => $livro->id,
            'quantidade' => 5
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'message' => 'Livro sem estoque disponível'
                ]);

        // Verificar que o estoque não foi alterado
        $livro->refresh();
        expect($livro->estoque)->toBe(2);
    });

    it('Teste complementar - Verifica se método temEstoque funciona corretamente', function () {
        $livro = Livro::factory()->create(['estoque' => 3]);

        expect($livro->temEstoque(1))->toBeTrue();
        expect($livro->temEstoque(3))->toBeTrue();
        expect($livro->temEstoque(4))->toBeFalse();
        expect($livro->temEstoque(0))->toBeTrue(); // 0 é menor ou igual a 3
    });

    it('Teste complementar - Verifica se método reduzirEstoque funciona corretamente', function () {
        $livro = Livro::factory()->create(['estoque' => 5]);

        $resultado = $livro->reduzirEstoque(2);

        expect($resultado)->toBeTrue();
        expect($livro->fresh()->estoque)->toBe(3);
    });

    it('Teste complementar - Verifica se método reduzirEstoque falha quando não há estoque suficiente', function () {
        $livro = Livro::factory()->create(['estoque' => 2]);

        $resultado = $livro->reduzirEstoque(5);

        expect($resultado)->toBeFalse();
        expect($livro->fresh()->estoque)->toBe(2); // Estoque não deve ter mudado
    });

    it('Teste complementar - Verifica se método adicionarEstoque funciona corretamente', function () {
        $livro = Livro::factory()->create(['estoque' => 3]);

        $resultado = $livro->adicionarEstoque(7);

        expect($resultado)->toBeTrue();
        expect($livro->fresh()->estoque)->toBe(10);
    });

    it('Teste complementar - Verifica se método emFalta funciona corretamente', function () {
        $livroSemEstoque = Livro::factory()->create(['estoque' => 0]);
        $livroComEstoque = Livro::factory()->create(['estoque' => 5]);

        expect($livroSemEstoque->emFalta())->toBeTrue();
        expect($livroComEstoque->emFalta())->toBeFalse();
    });

    it('Teste complementar - Verifica se método estoqueBaixo funciona corretamente', function () {
        $livroEstoqueBaixo = Livro::factory()->create(['estoque' => 3]);
        $livroEstoqueNormal = Livro::factory()->create(['estoque' => 10]);
        $livroSemEstoque = Livro::factory()->create(['estoque' => 0]);

        expect($livroEstoqueBaixo->estoqueBaixo())->toBeTrue(); // 3 <= 5 (padrão)
        expect($livroEstoqueNormal->estoqueBaixo())->toBeFalse(); // 10 > 5
        expect($livroSemEstoque->estoqueBaixo())->toBeFalse(); // 0 não é > 0

        // Testando com limite customizado
        expect($livroEstoqueBaixo->estoqueBaixo(2))->toBeFalse(); // 3 > 2
        expect($livroEstoqueBaixo->estoqueBaixo(5))->toBeTrue(); // 3 <= 5
    });

    it('Teste complementar - Verifica se atributo disponivel considera estoque', function () {
        $livroComEstoque = Livro::factory()->create(['estoque' => 5]);
        $livroSemEstoque = Livro::factory()->create(['estoque' => 0]);

        expect($livroComEstoque->disponivel)->toBeTrue();
        expect($livroSemEstoque->disponivel)->toBeFalse();
    });

    it('Teste complementar - Requisição múltipla reduz estoque corretamente', function () {
        $livro = Livro::factory()->create([
            'estoque' => 10,
            'nome' => 'Livro Requisição Múltipla',
        ]);

        // Primeira requisição
        $response1 = $this->postJson('/requisicoes', [
            'livro_id' => $livro->id,
            'quantidade' => 3
        ]);

        $response1->assertStatus(201);
        $livro->refresh();
        expect($livro->estoque)->toBe(7);

        // Segunda requisição
        $response2 = $this->postJson('/requisicoes', [
            'livro_id' => $livro->id,
            'quantidade' => 2
        ]);

        $response2->assertStatus(201);
        $livro->refresh();
        expect($livro->estoque)->toBe(5);
    });
});