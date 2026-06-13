<?php

namespace App\Console\Commands;

use App\Models\Pagamento;
use App\Services\Payments\PagamentoSync;
use Illuminate\Console\Command;

class SincronizarPagamentos extends Command
{
    protected $signature = 'pagamentos:sincronizar {--limit=100 : Máximo de pagamentos consultados por execução}';

    protected $description = 'Consulta o Confrapix (polling) e atualiza o status dos pagamentos PIX pendentes.';

    public function handle(PagamentoSync $sync): int
    {
        $pendentes = Pagamento::query()
            ->where('status', Pagamento::STATUS_PENDENTE)
            ->whereNotNull('charge_id')
            ->limit((int) $this->option('limit'))
            ->get();

        if ($pendentes->isEmpty()) {
            $this->info('Nenhum pagamento pendente para sincronizar.');

            return self::SUCCESS;
        }

        $atualizados = 0;

        foreach ($pendentes as $pagamento) {
            if ($sync->sync($pagamento)) {
                $atualizados++;
                $this->line("#{$pagamento->id} ({$pagamento->referencia}) → {$pagamento->status}");
            }
        }

        $this->info("Sincronização concluída: {$atualizados}/{$pendentes->count()} consultados.");

        return self::SUCCESS;
    }
}
