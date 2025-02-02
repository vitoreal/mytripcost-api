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
            $table->string('foto', 200);
            $table->string('mimetype', 100);
            $table->string('extension', 5);
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
