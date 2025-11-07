<?php

// database/migrations/2025_09_02_000002_create_dieta_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dieta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuario')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('origen', ['IA','manual'])->default('IA');
            $table->enum('estado', ['activa','finalizada'])->default('activa');
        });
    }

    public function down(): void {
        Schema::dropIfExists('dieta');
    }
};