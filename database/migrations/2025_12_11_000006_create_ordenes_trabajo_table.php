<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->foreignId('diagnostico_id')->constrained('diagnosticos')->onDelete('cascade');
            $table->foreignId('mecanico_id')->constrained('usuarios')->onDelete('restrict');
            $table->date('fecha_creacion')->default(DB::raw('CURRENT_DATE'));
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin_estimada')->nullable();
            $table->date('fecha_fin_real')->nullable();
            $table->decimal('costo_mano_obra', 10, 2)->default(0)->unsigned();
            $table->decimal('costo_repuestos', 10, 2)->default(0)->unsigned();
            $table->enum('estado', ['presupuestada', 'aprobada', 'en_proceso', 'completada', 'entregada', 'cancelada'])->default('presupuestada');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Agregar columna generada para subtotal
        DB::statement('ALTER TABLE ordenes_trabajo ADD COLUMN subtotal NUMERIC(10,2) GENERATED ALWAYS AS (costo_mano_obra + costo_repuestos) STORED');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_trabajo');
    }
};