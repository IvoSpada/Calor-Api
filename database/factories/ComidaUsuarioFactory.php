<?php
// database/factories/ComidaUsuarioFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Usuario;
use App\Models\ComidaDieta;

class ComidaUsuarioFactory extends Factory
{
    protected $model = \App\Models\ComidaUsuario::class;

    public function definition(): array
    {
        return [
            'usuario_id'      => Usuario::factory(),
            'comida_dieta_id' => $this->faker->optional()->randomElement([ComidaDieta::factory()]),
            'fecha'           => $this->faker->date(),
            'opcion'          => $this->faker->randomElement(['planificada','alternativa']),
            'descripcion'     => $this->faker->sentence(6),
            'calorias'        => $this->faker->numberBetween(200, 800),
            'proteinas'       => $this->faker->randomFloat(2, 5, 40),
            'carbohidratos'   => $this->faker->randomFloat(2, 10, 100),
            'grasas'          => $this->faker->randomFloat(2, 5, 30),
        ];
    }
}