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
        Schema::create('event_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Relación con evento
            $table->foreignId('speaker_id')->nullable()->constrained('professional_profiles')->onDelete('set null'); // Ponente (perfil profesional)
            $table->foreignId('proposed_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Propuesto por (usuario)
            $table->string('name'); // Nombre
            $table->text('description'); // Descripción
            $table->enum('type', ['activity', 'talk', 'workshop']); // Tipo de componente
            $table->enum('proposal_status', ['approved', 'proposed', 'rejected', 'offer_open'])->default('proposed'); // Estado de la propuesta
            $table->enum('modality', ['virtual','in_person','hybrid'])->default('in_person'); // Modalidad
            $table->string('location')->nullable(); // Ubicación
            $table->string('cover_image')->nullable(); // Imagen de portada
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->nullable(); // Nivel
            $table->integer('capacity')->nullable(); // Cupos
            $table->decimal('attendee_price', 10, 2)->nullable()->default(0); // Precio para asistentes
            $table->decimal('organizer_cost', 10, 2)->nullable(); // Costo para organizador
            $table->text('participant_requirements')->nullable(); // Requisitos para participantes
            $table->text('instructor_requirements')->nullable(); // Requisitos para instructores
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_components');
    }
};
