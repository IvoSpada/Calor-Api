<?php

// database/migrations/2025_09_02_000003_create_comida_dieta_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comida_dieta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dieta_id')->constrained('dieta')->onDelete('cascade');
            $table->date('fecha');
            // ESTA ES LA LÍNEA CLAVE CORREGIDA
            $table->enum('tipo', ['desayuno','almuerzo','cena','snack', 'merienda', 'colacion', 'media mañana', 'media tarde', 'post cena', 'postre']);
            $table->text('descripcion');
            $table->integer('calorias');
            $table->decimal('proteinas', 6, 2)->nullable();
            $table->decimal('carbohidratos', 6, 2)->nullable();
            $table->decimal('grasas', 6, 2)->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('comida_dieta');
    }
};