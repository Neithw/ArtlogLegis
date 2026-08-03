<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('camara_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('role_id')
                ->constrained()
                ->restrictOnDelete();

            $table->boolean('ativo')
                ->default(true);

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('camara_id');
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn('ativo');
            $table->dropSoftDeletes();
        });
    }
};
