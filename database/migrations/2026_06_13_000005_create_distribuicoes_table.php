<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribuicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('familia_id')->constrained('familias')->cascadeOnDelete();
            $table->foreignId('cesta_id')->nullable()->constrained('cestas')->nullOnDelete();
            $table->date('data_entrega');
            $table->string('responsavel')->nullable();
            $table->string('status')->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribuicoes');
    }
};
