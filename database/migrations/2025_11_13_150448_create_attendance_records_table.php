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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade'); // Inscripción relacionada
            $table->foreignId('schedule_id')->constrained('component_schedules')->onDelete('cascade'); // Horario relacionado
            $table->date('attendance_date'); // Fecha de asistencia
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null'); // Verificado por (usuario)
            $table->timestamps();

            $table->unique(['registration_id', 'schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
