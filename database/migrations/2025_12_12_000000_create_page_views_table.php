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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('page_name'); // Ej: 'dashboard', 'vehiculos', 'citas'
            $table->string('page_route'); // Ej: '/dashboard', '/mis-vehiculos'
            $table->string('ip_address')->nullable();
            $table->longText('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamps();

            // Índices para búsquedas rápidas
            $table->index('user_id');
            $table->index('page_name');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
