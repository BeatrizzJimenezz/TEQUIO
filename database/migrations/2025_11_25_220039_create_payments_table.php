<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Participante que paga
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade'); // Organizador que recibe
            $table->foreignId('component_id')->constrained('event_components')->onDelete('cascade');
            $table->string('paypal_transaction_id')->unique()->nullable();

            // Montos
            $table->decimal('component_price', 10, 2); // Precio del componente
            $table->decimal('platform_fee', 10, 2); // Comisión de la plataforma (5%)
            $table->decimal('paypal_fee', 10, 2); // Comisión de PayPal
            $table->decimal('total_paid', 10, 2); // Total pagado por participante
            $table->decimal('organizer_amount', 10, 2); // Monto que recibe organizador (= component_price)

            // Estado y método
            $table->enum('status', ['pending', 'completed', 'refunded', 'failed'])->default('pending');
            $table->enum('payment_method', ['online', 'in_person'])->default('online');

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index('organizer_id');
            $table->index('status');
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
