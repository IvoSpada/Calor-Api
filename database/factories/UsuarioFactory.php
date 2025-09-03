<?php

// database/factories/UsuarioFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    protected $model = \App\Models\Usuario::class;

    public function definition(): array
    {
        return [
            'nombre'   => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail(),
            'peso'     => $this->faker->randomFloat(2, 50, 120),
            'altura'   => $this->faker->randomFloat(2, 150, 200),
            'edad'     => $this->faker->numberBetween(18, 70),
            'objetivo' => $this->faker->randomElement(['perder_peso','mantener','ganar_peso']),
            'password' => $this->faker->password()
        ];
    }
}
