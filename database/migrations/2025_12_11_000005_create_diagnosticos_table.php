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
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            $table->foreignId('mecanico_id')->constrained('usuarios')->onDelete('restrict');
            $table->date('fecha_diagnostico');
            $table->text('descripcion_problema');
            $table->text('diagnostico');
            $table->text('recomendaciones')->nullable();
            $table->enum('estado', ['en_revision', 'completado', 'aprobado_cliente', 'rechazado_cliente'])->default('en_revision');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};