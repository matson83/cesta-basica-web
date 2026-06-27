<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fantasia');
            $table->string('razao_social')->nullable();
            $table->string('tipo_documento', 4)->default('cnpj');
            $table->string('documento', 18)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->text('endereco')->nullable();
            $table->text('confrapix_token')->nullable();
            $table->string('confrapix_base_url')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
