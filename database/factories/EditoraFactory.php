<?php

namespace Database\Factories;

use App\Models\Editora;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Editora>
 */
class EditoraFactory extends Factory
{
    protected $model = Editora::class;

    public function definition(): array
    {
        // Nomes realistas de editoras
        $editoras = [
            'Penguin Random House', 'HarperCollins', 'Macmillan Publishers',
            'Simon & Schuster', 'Hachette Book Group', 'Scholastic',
            'Pearson Education', 'Thomson Reuters', 'Wolters Kluwer',
            'RELX', 'Bertelsmann', 'News Corporation', 'Planeta',
            'Leya', 'Porto Editora', 'Editorial Presença', 'Dom Quixote'
        ];

        return [
            'nome' => $this->faker->randomElement($editoras),
            'logotipo' => '/images/default-book.png',
        ];
    }

    /**
     * Editora portuguesa
     */
    public function portuguesa(): static
    {
        $editorasPortuguesas = [
            'Leya', 'Porto Editora', 'Editorial Presença', 
            'Dom Quixote', 'Gradiva', 'Teorema', 'Casa das Letras'
        ];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($editorasPortuguesas),
        ]);
    }
}