<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('categoria')->nullable();
            $table->string('unidade')->default('unidade');
            $table->unsignedInteger('estoque')->default(0);
            $table->unsignedInteger('quantidade_por_cesta')->default(1);
            $table->decimal('preco', 10, 2)->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
