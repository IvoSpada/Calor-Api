<?php
// database/factories/DietaFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Usuario;

class DietaFactory extends Factory
{
    protected $model = \App\Models\Dieta::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', 'now');
        $end   = (clone $start)->modify('+7 days');

        return [
            'usuario_id'   => Usuario::factory(),
            'fecha_inicio' => $start->format('Y-m-d'),
            'fecha_fin'    => $end->format('Y-m-d'),
            'origen'       => $this->faker->randomElement(['IA','manual']),
            'estado'       => $this->faker->randomElement(['activa','finalizada']),
        ];
    }
}