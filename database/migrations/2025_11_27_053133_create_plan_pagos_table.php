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
        Schema::create('plan_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('estado');
            $table->date('fechainicio');
            $table->date('fechafin')->nullable();
            $table->float('montoporcuota');
            $table->float('montototal');
            $table->integer('numerocuotas');
            $table->string('observacion')->nullable();
            $table->foreignId('orden_trabajo_id')->constrained('orden_trabajos');
            $table->unique('orden_trabajo_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_pagos');
    }
};