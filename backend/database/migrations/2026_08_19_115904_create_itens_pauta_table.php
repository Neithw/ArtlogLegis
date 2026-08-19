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
        Schema::create('itens_pauta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sessao_id')
                ->constrained('sessoes')
                ->restrictOnDelete();

            $table->foreignId('proposicao_id')
                ->constrained('proposicoes')
                ->restrictOnDelete();

            $table->foreignId('incluido_por_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('ordem');

            $table->string('situacao')->default('pendente');

            $table->timestamps();
            $table->unique(['sessao_id', 'proposicao_id']);
            $table->unique(['sessao_id', 'ordem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itens_pauta');
    }
};
