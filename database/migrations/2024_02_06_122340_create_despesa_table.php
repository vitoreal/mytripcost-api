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
        Schema::create('despesa', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 200);
            $table->decimal('valor');
            $table->date('data_despesa')->nullable();
            $table->foreignId('id_moeda')->references('id')->on('moeda');
            $table->foreignId('id_viagem')->references('id')->on('viagem');
            $table->foreignId('id_categoria')->references('id')->on('categoria')->nullable();
            $table->foreignId('id_categoria_personalizada')->references('id')->on('categoria_personalizada')->nullable();
            $table->foreignId('id_metodo_pagamento')->references('id')->on('metodo_pagamento')->nullable();
            $table->string('outros_metodo_pagamento', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despesa');
    }
};
