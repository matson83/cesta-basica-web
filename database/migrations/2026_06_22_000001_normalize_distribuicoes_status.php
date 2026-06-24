<?php

use App\Models\Distribuicao;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('distribuicoes')
            ->whereIn('status', ['paga', 'entregue'])
            ->update(['status' => Distribuicao::STATUS_PAGO]);

        DB::table('distribuicoes')
            ->where('status', 'cancelada')
            ->update(['status' => Distribuicao::STATUS_CANCELADO]);
    }

    public function down(): void
    {
        DB::table('distribuicoes')
            ->where('status', Distribuicao::STATUS_PAGO)
            ->update(['status' => 'paga']);

        DB::table('distribuicoes')
            ->where('status', Distribuicao::STATUS_CANCELADO)
            ->update(['status' => 'cancelada']);
    }
};
