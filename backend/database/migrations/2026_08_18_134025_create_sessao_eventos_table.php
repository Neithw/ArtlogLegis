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
        Schema::create('sessao_eventos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sessao_id')
                ->constrained('sessoes')
                ->restrictOnDelete();

            $table->foreignId('executado_por_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('acao');
            $table->string('situacao_anterior');
            $table->string('situacao_nova');
            $table->text('observacao')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessao_eventos');
    }
};
