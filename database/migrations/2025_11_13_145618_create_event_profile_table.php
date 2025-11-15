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
        Schema::create('event_profiles', function (Blueprint $table) {
            $table->id(); // ID principal
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade'); // Relación con evento
            $table->foreignId('professional_profile_id')->constrained('professional_profiles')->onDelete('cascade'); // Relación con perfil profesional
            $table->string('role'); // Rol en el evento
            $table->timestamps();

            $table->unique(['event_id', 'professional_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_profiles');
    }
};
