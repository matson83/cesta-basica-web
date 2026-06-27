<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impulsionamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestor_id')->constrained('gestores')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('mensagem');
            $table->json('imagens')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impulsionamentos');
    }
};
