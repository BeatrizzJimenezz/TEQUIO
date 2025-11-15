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
        Schema::create('academic_trainings', function (Blueprint $table) {
            $table->id(); // Id principal
            $table->foreignId('professional_profile_id')->constrained('professional_profiles')->onDelete('cascade'); // Relación 1 a N
            $table->string('institution'); // Institución educativa
            $table->string('degree'); // Título obtenido
            $table->date('start_date'); // Fecha de inicio
            $table->date('end_date')->nullable(); // Fecha fin, nullable si aún estudia
            $table->text('description')->nullable(); // Descripción adicional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_trainings');
    }
};
