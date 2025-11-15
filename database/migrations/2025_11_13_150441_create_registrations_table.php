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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('event_components')->onDelete('cascade'); // Relación con componente
            $table->string('ticket_qr')->unique(); // Código QR del ticket
            $table->timestamp('registered_at')->useCurrent(); // Fecha de inscripción
            $table->timestamp('expires_at')->nullable(); // Fecha de caducidad
            $table->timestamps();

            $table->unique(['user_id', 'component_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
