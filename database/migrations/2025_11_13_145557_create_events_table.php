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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_profile_id')->constrained('professional_profiles')->onDelete('cascade'); // Relación con perfil profesional
            $table->string('name'); // Nombre
            $table->date('start_date'); // Fecha de inicio
            $table->date('end_date'); // Fecha fin
            $table->time('start_time'); // Hora de inicio
            $table->text('description'); // Descripción
            $table->string('cover_image')->nullable(); // Imagen de portada
            $table->string('logo')->nullable(); // Logo
            $table->enum('modality', ['virtual','in_person','hybrid'])->default('in_person'); // Modalidad
            $table->string('location')->nullable(); // Ubicación
            $table->enum('visibility', ['public','private'])->default('public'); // Visibilidad
            $table->enum('status', ['planning','active','finished'])->default('planning'); // Estado del evento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
