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
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->unsignedInteger('numero')->nullable();
            $table->unsignedSmallInteger('ano')->nullable();

            $table->foreignId('protocolado_por_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('data_protocolo')->nullable();

            $table->unique(
                ['camara_id', 'tipo_proposicao_id', 'ano', 'numero'],
                'proposicoes_numeracao_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropUnique('proposicoes_numeracao_unique');
            $table->dropConstrainedForeignId('protocolado_por_id');

            $table->dropColumn([
                'numero',
                'ano',
                'data_protocolo'
            ]);
        });
    }
};
