<?php

// database/seeders/ComidaUsuarioSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComidaUsuario;

class ComidaUsuarioSeeder extends Seeder
{
    public function run(): void
    {
        ComidaUsuario::factory()->count(5)->create();
    }
}
