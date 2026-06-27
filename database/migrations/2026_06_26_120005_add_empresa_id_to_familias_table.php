<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->foreignId('empresa_id')->nullable()->after('id')->constrained('empresas')->nullOnDelete();
        });

        // O CPF passa a ser único por firma (EC), não mais globalmente.
        Schema::table('familias', function (Blueprint $table) {
            $table->dropUnique('familias_cpf_unique');
            $table->unique(['empresa_id', 'cpf']);
        });
    }

    public function down(): void
    {
        Schema::table('familias', function (Blueprint $table) {
            $table->dropUnique(['empresa_id', 'cpf']);
            $table->dropConstrainedForeignId('empresa_id');
            $table->unique('cpf');
        });
    }
};
