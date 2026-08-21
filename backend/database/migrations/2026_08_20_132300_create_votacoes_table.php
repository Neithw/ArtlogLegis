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
        Schema::create('votacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_pauta_id')
                ->constrained('itens_pauta')
                ->restrictOnDelete();

            $table->foreignId('aberta_por_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('encerrada_por_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('cancelada_por_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('tipo')->default('nominal');
            $table->string('criterio_aprovacao')->default('maioria_simples');
            $table->string('situacao')->default('aberta');
            $table->string('resultado')->nullable();

            $table->timestamp('aberta_em');
            $table->timestamp('encerrada_em')->nullable();
            $table->timestamp('cancelada_em')->nullable();

            $table->text('observacao')->nullable();
            $table->text('motivo_cancelamento')->nullable();

            $table->timestamps();

            $table->index(['item_pauta_id', 'situacao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votacoes');
    }
};
