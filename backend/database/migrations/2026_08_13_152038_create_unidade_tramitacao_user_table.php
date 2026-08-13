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
        Schema::create('unidade_tramitacao_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unidade_tramitacao_id')
                ->constrained('unidades_tramitacao')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['unidade_tramitacao_id', 'user_id'],
                'unidade_tramitacao_user_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidade_tramitacao_user');
    }
};
