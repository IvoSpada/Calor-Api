<?php

// database/factories/ComidaDietaFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Dieta;

class ComidaDietaFactory extends Factory
{
    protected $model = \App\Models\ComidaDieta::class;

    public function definition(): array
    {
        return [
            'dieta_id'     => Dieta::factory(),
            'fecha'        => $this->faker->date(),
            'tipo'         => $this->faker->randomElement(['desayuno','almuerzo','cena','snack']),
            'descripcion'  => $this->faker->sentence(6),
            'calorias'     => $this->faker->numberBetween(200, 800),
            'proteinas'    => $this->faker->randomFloat(2, 5, 40),
            'carbohidratos'=> $this->faker->randomFloat(2, 10, 100),
            'grasas'       => $this->faker->randomFloat(2, 5, 30),
        ];
    }
}