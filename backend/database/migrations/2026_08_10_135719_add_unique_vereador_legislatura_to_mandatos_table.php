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
        Schema::table('mandatos', function (Blueprint $table) {
            $table->unique(
                ['vereador_id', 'legislatura_id'],
                'mandatos_vereador_legislatura_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mandatos', function (Blueprint $table) {
            $table->dropUnique(
                'mandatos_vereador_legislatura_unique'
            );
        });
    }
};
