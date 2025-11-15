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
        Schema::create('offer_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained('event_components')->onDelete('cascade'); // Relación con componente
            $table->foreignId('professional_profile_id')->constrained('professional_profiles')->onDelete('cascade'); // Perfil profesional que aplica
            $table->text('message')->nullable(); // Mensaje del aplicante
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending'); // Estado de la aplicación
            $table->timestamps();

            // Un usuario solo puede aplicar una vez a la misma oferta
            $table->unique(['component_id', 'professional_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_applications');
    }
};
