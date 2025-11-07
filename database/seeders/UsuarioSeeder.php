<?php

// database/seeders/UsuarioSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::factory()->count(1)->create();
    }
}
