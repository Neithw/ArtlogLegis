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
        Schema::create('mandatos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vereador_id')
                ->constrained('vereadores')
                ->restrictOnDelete();

            $table->foreignId('legislatura_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('data_inicio');
            $table->date('data_fim')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandatos');
    }
};
