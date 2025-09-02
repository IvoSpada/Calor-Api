<?php

// database/migrations/2025_09_02_000004_create_comida_usuario_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comida_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuario')->onDelete('cascade');
            $table->foreignId('comida_dieta_id')->nullable()->constrained('comida_dieta')->onDelete('set null');
            $table->date('fecha');
            $table->enum('opcion', ['planificada','alternativa']);
            $table->text('descripcion');
            $table->integer('calorias');
            $table->decimal('proteinas', 6, 2)->nullable();
            $table->decimal('carbohidratos', 6, 2)->nullable();
            $table->decimal('grasas', 6, 2)->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('comida_usuario');
    }
};