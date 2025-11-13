<?php
// database/migrations/2025_09_02_000001_create_usuario_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('password');
            $table->string('email', 100)->unique();
            $table->decimal('peso', 5, 2)->nullable();   // en kg
            $table->decimal('altura', 5, 2)->nullable(); // en cm
            
            // --- Campos actualizados (de nullable a requerido por el controller) ---
            $table->integer('edad');
            $table->enum('objetivo', ['perder_peso','mantener','ganar_peso']);

            // --- NUEVOS CAMPOS AÑADIDOS ---
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();
            $table->text('patologias')->nullable();
            $table->text('ejercicio')->nullable();
            $table->tinyInteger('premium')->default(0); // 0=No, 1=Premium, 2=Premium++
        });
    }

    public function down(): void {
        Schema::dropIfExists('usuario');
    }
};