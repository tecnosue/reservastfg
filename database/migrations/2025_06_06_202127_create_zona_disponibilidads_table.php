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
        Schema::create('zona_disponibilidads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zona_id')->constrained()->onDelete('cascade');
            $table->enum('dia_semana', ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zona_disponibilidads');
    }
};
// Este archivo crea la tabla 'zona_disponibilidads' que almacena la disponibilidad de las zonas por día de la semana y horario.
//Laravel por convención busca el nombre de la tabla en plural automáticamente, como el modelo se llama ZonaDisponibilidad, la tabla se llamará zona_disponibilidads.
// La tabla tiene una relación con la tabla 'zonas' a través de 'zona_id', y almacena el día de la semana y el horario de inicio y fin de disponibilidad.