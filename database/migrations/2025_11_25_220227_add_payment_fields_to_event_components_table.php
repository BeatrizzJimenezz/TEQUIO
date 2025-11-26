<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_components', function (Blueprint $table) {
            $table->boolean('payment_required')->default(false)->after('capacity');
            $table->decimal('price', 10, 2)->default(0.00)->after('payment_required');
            $table->json('payment_methods')->nullable()->after('price'); // ["online", "in_person"]
            $table->boolean('allow_multiple_payment_methods')->default(false)->after('payment_methods');
        });
    }

    public function down(): void
    {
        Schema::table('event_components', function (Blueprint $table) {
            $table->dropColumn([
                'payment_required',
                'price',
                'payment_methods',
                'allow_multiple_payment_methods'
            ]);
        });
    }
};
