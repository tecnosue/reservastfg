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
        Schema::create('reglas_reserva', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('max_por_dia')->default(0);
            $table->unsignedInteger('max_por_semana')->default(0);
            $table->unsignedInteger('max_por_mes')->default(0);
            $table->unsignedInteger('max_por_anio')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas_reserva');
    }
};
