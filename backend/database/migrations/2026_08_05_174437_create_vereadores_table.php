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
        Schema::create('vereadores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('camara_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('nome');
            $table->string('nome_parlamentar')->nullable();

            $table->string('email_institucional')->nullable();
            $table->string('telefone_institucional')->nullable();

            $table->text('biografia')->nullable();
            $table->string('foto_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vereadores');
    }
};
