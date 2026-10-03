<?php

namespace App\Console\Commands;

use App\Jobs\ReconcilePaymentShare;
use App\Models\PaymentShare;
use Illuminate\Console\Command;

class ReconcileSplitPayments extends Command
{
    protected $signature = 'payments:reconcile-splits {--limit=100 : Maximum share checks per run}';

    protected $description = 'Queue bounded status checks for recent sandbox split-bill transactions';

    public function handle(): int
    {
        if (config('platform.midtrans_production') || ! config('platform.midtrans_server_key')) {
            $this->info('Sandbox split bill is not enabled.');

            return self::SUCCESS;
        }
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 500]]);
        if ($limit === false) {
            $this->error('Limit must be between 1 and 500.');

            return self::INVALID;
        }
        $shares = PaymentShare::whereIn('status', ['pending', 'paid', 'expired', 'cancelled', 'failed'])->whereBetween('created_at', [now()->subDays(3), now()->subMinute()])->where(fn ($query) => $query->whereNull('reconciled_at')->orWhere('reconciled_at', '<=', now()->subMinutes(5)))->orderBy('reconciled_at')->orderBy('id')->limit($limit)->get(['reference']);
        foreach ($shares as $share) {
            ReconcilePaymentShare::dispatch($share->reference);
        }
        $this->info($shares->count().' share checks dispatched.');

        return self::SUCCESS;
    }
}
