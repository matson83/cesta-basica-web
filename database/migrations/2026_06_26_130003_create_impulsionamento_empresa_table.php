<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impulsionamento_empresa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impulsionamento_id')->constrained('impulsionamentos')->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamps();

            $table->unique(['impulsionamento_id', 'empresa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impulsionamento_empresa');
    }
};
