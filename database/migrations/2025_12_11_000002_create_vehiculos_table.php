<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('placa', 20)->unique();
            $table->string('marca', 50);
            $table->string('modelo', 50);
            $table->integer('anio')->nullable();
            $table->string('color', 30)->nullable();
            $table->integer('kilometraje')->default(0);
            $table->text('foto')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};