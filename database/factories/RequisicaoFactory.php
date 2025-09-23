<?php

namespace Database\Factories;

use App\Models\Requisicao;
use App\Models\User;
use App\Models\Livro;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequisicaoFactory extends Factory
{
    protected $model = Requisicao::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'livro_id' => Livro::factory(),
            'ativo' => true,
            'data_recepcao' => null,
            'dias_decorridos' => null,
        ];
    }

    /**
     * Requisição ativa
     */
    public function ativa()
    {
        return $this->state(function (array $attributes) {
            return [
                'ativo' => true,
                'data_recepcao' => null,
            ];
        });
    }

    /**
     * Requisição devolvida
     */
    public function devolvida()
    {
        return $this->state(function (array $attributes) {
            return [
                'ativo' => false,
                'data_recepcao' => $this->faker->dateTimeBetween('-30 days', 'now'),
                'dias_decorridos' => $this->faker->numberBetween(1, 30),
            ];
        });
    }
}