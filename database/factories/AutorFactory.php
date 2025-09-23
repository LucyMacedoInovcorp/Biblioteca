<?php

namespace Database\Factories;

use App\Models\Autor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Autor>
 */
class AutorFactory extends Factory
{
    protected $model = Autor::class;

    public function definition(): array
    {
        // Nomes realistas de autores
        $autores = [
            'José Saramago', 'Fernando Pessoa', 'Eça de Queirós',
            'Machado de Assis', 'Clarice Lispector', 'Paulo Coelho',
            'Miguel Torga', 'Sophia de Mello Breyner', 'António Lobo Antunes',
            'J.K. Rowling', 'Stephen King', 'Agatha Christie',
            'George Orwell', 'Jane Austen', 'Charles Dickens'
        ];

        return [
            'nome' => $this->faker->randomElement($autores),
            'foto' => '/images/default-book.png',
        ];
    }

    /**
     * Autor português
     */
    public function portugues(): static
    {
        $autoresPortugueses = [
            'José Saramago', 'Fernando Pessoa', 'Eça de Queirós',
            'Miguel Torga', 'Sophia de Mello Breyner', 'António Lobo Antunes',
            'Almeida Garrett', 'Camilo Castelo Branco', 'Aquilino Ribeiro'
        ];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($autoresPortugueses),
        ]);
    }

    /**
     * Autor internacional
     */
    public function internacional(): static
    {
        $autoresInternacionais = [
            'J.K. Rowling', 'Stephen King', 'Agatha Christie',
            'George Orwell', 'Jane Austen', 'Charles Dickens',
            'Gabriel García Márquez', 'Mario Vargas Llosa'
        ];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($autoresInternacionais),
        ]);
    }
}