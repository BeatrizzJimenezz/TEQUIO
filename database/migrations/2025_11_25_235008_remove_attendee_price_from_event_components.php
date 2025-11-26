<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primero, copiar los datos de attendee_price a price si price está vacío
        DB::statement('UPDATE event_components SET price = attendee_price WHERE price = 0 OR price IS NULL');

        // Luego eliminar la columna attendee_price
        Schema::table('event_components', function (Blueprint $table) {
            $table->dropColumn('attendee_price');
        });
    }

    public function down(): void
    {
        // Restaurar la columna si se hace rollback
        Schema::table('event_components', function (Blueprint $table) {
            $table->decimal('attendee_price', 10, 2)->nullable()->default(0)->after('capacity');
        });

        // Copiar price de vuelta a attendee_price
        DB::statement('UPDATE event_components SET attendee_price = price');
    }
};
