<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->enum('payment_status', ['free', 'pending', 'paid', 'failed', 'refunded'])->default('pending')->after('expires_at');
            $table->enum('payment_method', ['online', 'in_person'])->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_method', 'paid_at']);
        });
    }
};
