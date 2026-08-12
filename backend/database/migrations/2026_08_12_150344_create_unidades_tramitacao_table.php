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
        Schema::create('unidades_tramitacao', function (Blueprint $table) {
            $table->id();

            $table->foreignId('camara_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('nome');
            $table->string('sigla')->nullable();
            $table->string('tipo');
            $table->text('descricao')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['camara_id', 'nome'],
                'unidades_tramitacao_camara_nome_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades_tramitacao');
    }
};
