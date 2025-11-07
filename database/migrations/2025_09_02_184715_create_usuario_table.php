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
            $table->integer('edad')->nullable();
            $table->enum('objetivo', ['perder_peso','mantener','ganar_peso'])->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('usuario');
    }
};