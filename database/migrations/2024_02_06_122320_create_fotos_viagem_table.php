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
        Schema::create('fotos_viagem', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->binary('arquivo');
            $table->foreignId('id_viagem')->references('id')->on('viagem')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fotos_viagem');
    }
};
