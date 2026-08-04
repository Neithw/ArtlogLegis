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
        Schema::create('legislaturas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('camara_id')
                ->constrained()
                ->restrictOnDelete();

            $table->unsignedSmallInteger('numero');

            $table->date('data_inicio');
            $table->date('data_fim');

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'camara_id',
                'numero'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legislaturas');
    }
};
