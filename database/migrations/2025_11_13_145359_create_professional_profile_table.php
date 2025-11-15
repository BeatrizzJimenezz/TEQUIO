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
        Schema::create('professional_profiles', function (Blueprint $table) {
            $table->id(); // Id principal de la tabla
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación 1 a 1 con usuarios
            $table->text('about_me')->nullable(); // Bibliografía
            $table->string('current_workplace')->nullable(); // Lugar de trabajo
            $table->string('skills')->nullable(); // Skills separados por coma
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_profiles');
    }
};
