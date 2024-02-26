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
        Schema::create('viagem', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->decimal('orcamento');
            $table->string('descricao', 1000)->nullable();
            $table->string('foto', 200)->nullable();
            $table->boolean('privado');
            $table->foreignId('id_moeda')->references('id')->on('moeda');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viagem');
    }
};
