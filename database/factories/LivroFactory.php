<?php

namespace Database\Factories;

use App\Models\Livro;
use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livro>
 */
class LivroFactory extends Factory
{
    protected $model = Livro::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->sentence(rand(2, 4)), // "O Grande Livro"
            'ISBN' => $this->faker->isbn13(), // "978-3-16-148410-0"
            'bibliografia' => $this->faker->paragraphs(rand(2, 4), true), // Texto longo
            'preco' => $this->faker->randomFloat(2, 5, 100), // 5.00 a 100.00
            'imagemcapa' => '/images/default-book.png',
            'disponivel' => true,
            'editora_id' => Editora::factory(), // Cria automaticamente uma editora
        ];
    }

    /**
     * Estado: Livro disponível
     */
    public function disponivel(): static
    {
        return $this->state(fn (array $attributes) => [
            'disponivel' => true,
        ]);
    }

    /**
     * Estado: Livro indisponível (requisitado)
     */
    public function indisponivel(): static
    {
        return $this->state(fn (array $attributes) => [
            'disponivel' => false,
        ]);
    }

    /**
     * Estado: Livro caro
     */
    public function caro(): static
    {
        return $this->state(fn (array $attributes) => [
            'preco' => $this->faker->randomFloat(2, 80, 200),
        ]);
    }

    /**
     * Estado: Livro barato
     */
    public function barato(): static
    {
        return $this->state(fn (array $attributes) => [
            'preco' => $this->faker->randomFloat(2, 1, 15),
        ]);
    }

    /**
     * Estado: Livro com dados específicos
     */
    public function comNome(string $nome): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => $nome,
        ]);
    }

    /**
     * Estado: Livro de uma editora específica
     */
    public function daEditora(int $editoraId): static
    {
        return $this->state(fn (array $attributes) => [
            'editora_id' => $editoraId,
        ]);
    }
}