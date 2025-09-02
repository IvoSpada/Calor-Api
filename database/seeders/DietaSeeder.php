<?php
// database/seeders/DietaSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dieta;

class DietaSeeder extends Seeder
{
    public function run(): void
    {
        Dieta::factory()->count(100)->create();
    }
}