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
        Schema::create('component_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('event_components')->onDelete('cascade'); // Relación con componente
            $table->date('date'); // Fecha
            $table->time('start_time'); // Hora inicio
            $table->time('end_time'); // Hora fin
            $table->timestamps();

            $table->unique(['component_id', 'date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_schedules');
    }
};
