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
        Schema::create('tipos_proposicao', function (Blueprint $table) {
            $table->id();

            $table->foreignId('camara_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('nome');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['camara_id', 'nome']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_proposicao');
    }
};
