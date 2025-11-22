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
        Schema::table('professional_profiles', function (Blueprint $table) {
            // Hacer user_id nullable para perfiles temporales
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Campos para perfiles temporales
            $table->boolean('is_temporary')->default(false)->after('user_id');
            $table->string('temp_email')->nullable()->after('is_temporary');
            $table->string('temp_name')->nullable()->after('temp_email');
            $table->string('temp_profession')->nullable()->after('temp_name');
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('temp_profession');

            // Índice para búsqueda por email temporal
            $table->index('temp_email');

            // Foreign key para quien creó el perfil temporal
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_profiles', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropIndex(['temp_email']);
            $table->dropColumn([
                'is_temporary',
                'temp_email',
                'temp_name',
                'temp_profession',
                'created_by_user_id'
            ]);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
