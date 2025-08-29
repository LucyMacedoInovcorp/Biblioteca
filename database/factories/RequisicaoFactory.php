<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Livro;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requisicao>
 */
class RequisicaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),   // gera um utilizador fake
            'livro_id' => Livro::factory(), // gera um livro fake
            'ativo' => $this->faker->boolean(),
            'data_recepcao' => $this->faker->dateTimeBetween('now', '+10 days'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
