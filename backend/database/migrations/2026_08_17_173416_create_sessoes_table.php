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
        Schema::create('sessoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('camara_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('legislatura_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('criado_por_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('numero')->nullable();
            $table->unsignedSmallInteger('ano')->nullable();
            $table->dateTime('data_hora_inicio_previsto');

            $table->string('tipo');
            $table->string('local')->nullable();
            $table->string('situacao')->default('em_preparacao');

            $table->timestamps();
            $table->unique([
                'legislatura_id',
                'tipo',
                'ano',
                'numero'
            ], 'sessoes_numeracao_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessoes');
    }
};
