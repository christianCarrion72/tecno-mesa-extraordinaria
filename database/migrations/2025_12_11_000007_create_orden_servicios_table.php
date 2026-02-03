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
        Schema::create('orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->onDelete('cascade');
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('restrict');
            $table->text('descripcion')->nullable();
            $table->integer('cantidad')->default(1)->unsigned();
            $table->decimal('precio_unitario', 10, 2)->unsigned();
        });

        // Agregar columna generada para subtotal
        DB::statement('ALTER TABLE orden_servicios ADD COLUMN subtotal NUMERIC(10,2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_servicios');
    }
};