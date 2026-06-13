<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('familias', function (Blueprint $table) {
            $table->id();
            $table->string('nome_responsavel');
            $table->string('cpf', 14)->unique();
            $table->unsignedInteger('num_membros')->default(1);
            $table->string('telefone')->nullable();
            $table->string('bairro')->nullable();
            $table->text('endereco')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('familias');
    }
};
