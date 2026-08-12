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
        Schema::create('tramitacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proposicao_id')
                ->constrained('proposicoes')
                ->restrictOnDelete();

            $table->foreignId('unidade_origem_id')
                ->nullable()
                ->constrained('unidades_tramitacao')
                ->restrictOnDelete();

            $table->foreignId('unidade_destino_id')
                ->constrained('unidades_tramitacao')
                ->restrictOnDelete();

            $table->foreignId('encaminhado_por_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('recebido_por_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('data_encaminhamento');
            $table->timestamp('data_recebimento')->nullable();

            $table->text('despacho')->nullable();

            $table->timestamps();

            $table->index(
                ['proposicao_id', 'data_encaminhamento'],
                'tramitacoes_proposicao_data_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tramitacoes');
    }
};
