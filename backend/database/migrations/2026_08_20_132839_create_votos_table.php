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
        Schema::create('votos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('votacao_id')
                ->constrained('votacoes')
                ->restrictOnDelete();

            $table->foreignId('mandato_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('registrado_por_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('atualizado_por_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('escolha');

            $table->timestamps();

            $table->unique(['votacao_id', 'mandato_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};
