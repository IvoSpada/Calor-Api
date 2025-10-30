<?php

// database/seeders/ComidaDietaSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ComidaDieta;

class ComidaDietaSeeder extends Seeder
{
    public function run(): void
    {
        ComidaDieta::factory()->count(5)->create();
    }
}