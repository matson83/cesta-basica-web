<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('referencia')->unique();
            $table->string('charge_id')->nullable()->index();
            $table->string('metodo')->default('pix');
            $table->string('status')->default('pendente');
            $table->unsignedBigInteger('valor_centavos');
            $table->string('pagador_nome')->nullable();
            $table->string('pagador_cpf', 14)->nullable();
            $table->string('pagador_email')->nullable();
            $table->string('pagador_telefone')->nullable();
            $table->text('pix_copia_cola')->nullable();
            $table->longText('pix_qr_code_base64')->nullable();
            $table->timestamp('expira_em')->nullable();
            $table->json('payload_gateway')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
