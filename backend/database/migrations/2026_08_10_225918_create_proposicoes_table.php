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
        Schema::create('proposicoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('camara_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('legislatura_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('tipo_proposicao_id')
                ->constrained('tipos_proposicao')
                ->restrictOnDelete();

            $table->foreignId('autor_mandato_id')
                ->constrained('mandatos')
                ->restrictOnDelete();

            $table->foreignId('criado_por_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('ementa')->nullable();
            $table->longText('texto_integral')->nullable();
            $table->string('assunto')->nullable();
            $table->string('area_tematica')->nullable();
            $table->json('palavras_chave')->nullable();

            $table->string('situacao')->default('rascunho');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposicoes');
    }
};
