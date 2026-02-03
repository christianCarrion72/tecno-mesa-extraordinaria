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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->onDelete('restrict');
            $table->decimal('monto_total', 10, 2)->unsigned();
            $table->decimal('monto_pagado', 10, 2)->default(0)->unsigned();
            $table->enum('tipo_pago', ['contado', 'credito']);
            $table->integer('numero_cuotas')->default(1)->unsigned();
            $table->integer('cuotas_pagadas')->default(0)->unsigned();
            $table->enum('estado', ['pendiente', 'pagado_parcial', 'pagado_total', 'vencido'])->default('pendiente');
            $table->date('fecha_vencimiento')->nullable(); // Para créditos
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Agregar columna generada para monto_pendiente
        DB::statement('ALTER TABLE pagos ADD COLUMN monto_pendiente NUMERIC(10,2) GENERATED ALWAYS AS (monto_total - monto_pagado) STORED');

        // Agregar constraints CHECK
        DB::statement('ALTER TABLE pagos ADD CONSTRAINT cuotas_validas CHECK (cuotas_pagadas <= numero_cuotas)');
        DB::statement('ALTER TABLE pagos ADD CONSTRAINT credito_tiene_cuotas CHECK ((tipo_pago = \'contado\' AND numero_cuotas = 1) OR (tipo_pago = \'credito\' AND numero_cuotas > 1))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE pagos DROP CONSTRAINT IF EXISTS cuotas_validas');
        DB::statement('ALTER TABLE pagos DROP CONSTRAINT IF EXISTS credito_tiene_cuotas');
        Schema::dropIfExists('pagos');
    }
};