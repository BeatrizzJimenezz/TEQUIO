<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizer_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            // Balances
            $table->decimal('available_balance', 10, 2)->default(0); // Disponible para retiro
            $table->decimal('pending_balance', 10, 2)->default(0); // En proceso
            $table->decimal('total_earned', 10, 2)->default(0); // Total ganado histórico
            $table->decimal('total_withdrawn', 10, 2)->default(0); // Total retirado histórico

            // Información de PayPal para retiros
            $table->string('paypal_email')->nullable();

            $table->timestamps();

            // Índice
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizer_balances');
    }
};
