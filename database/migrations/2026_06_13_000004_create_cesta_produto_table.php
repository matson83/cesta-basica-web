<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cesta_produto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cesta_id')->constrained('cestas')->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->unsignedInteger('quantidade')->default(1);
            $table->timestamps();

            $table->unique(['cesta_id', 'produto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cesta_produto');
    }
};
