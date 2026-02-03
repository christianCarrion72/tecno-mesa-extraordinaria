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
        Schema::create('pago_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->onDelete('cascade');
            $table->integer('numero_cuota')->unsigned();
            $table->decimal('monto', 10, 2)->unsigned();
            $table->enum('metodo_pago', ['efectivo', 'qr']);
            $table->string('numero_comprobante', 50)->nullable();
            $table->string('banco', 50)->nullable();
            $table->text('referencia')->nullable();
            $table->date('fecha_pago')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora_pago')->default(DB::raw('CURRENT_TIME'));
            $table->foreignId('recibido_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_detalles');
    }
};