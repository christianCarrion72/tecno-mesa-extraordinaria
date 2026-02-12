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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('estado');
            $table->date('fechapago');
            $table->string('metodopago');
            $table->float('monto');
            $table->integer('numerocuota');
            $table->string('referencia')->nullable();
            $table->string('pf_transaction_id')->nullable();
            $table->string('pf_payment_method_transaction_id')->nullable();
            $table->integer('pf_status')->nullable();
            $table->dateTime('pf_expiration_date')->nullable();
            $table->longText('pf_qr_base64')->nullable();
            $table->foreignId('plan_pago_id')->constrained('plan_pagos');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
