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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->foreignId('cliente_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora');
            $table->text('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'confirmada', 'en_proceso', 'completada', 'cancelada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};