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
        Schema::create('reportes_cache', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_reporte', 50);
            $table->string('periodo', 20)->nullable(); // 'diario', 'semanal', 'mensual', 'anual'
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->json('datos');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_cache');
    }
};