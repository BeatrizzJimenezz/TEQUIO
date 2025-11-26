<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->decimal('amount', 10, 2); // Monto solicitado
            $table->string('paypal_transaction_id')->nullable(); // ID de transferencia de PayPal
            $table->string('paypal_email'); // Email destino

            $table->enum('status', ['pending', 'processing', 'completed', 'rejected', 'failed'])->default('pending');

            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();

            $table->text('notes')->nullable(); // Notas del admin
            $table->timestamps();

            // Índices
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
