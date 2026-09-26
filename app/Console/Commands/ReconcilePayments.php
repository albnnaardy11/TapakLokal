<?php

namespace App\Console\Commands;

use App\Jobs\ReconcilePayment;
use App\Models\Payment;
use Illuminate\Console\Command;

class ReconcilePayments extends Command
{
    protected $signature = 'payments:reconcile {--limit=500 : Maximum payments dispatched per run}';

    protected $description = 'Queue bounded checks for recent pending gateway payments';

    public function handle(): int
    {
        if (! config('platform.midtrans_server_key')) {
            $this->info('Gateway is not configured; no checks dispatched.');

            return self::SUCCESS;
        }
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5000]]);
        if ($limit === false) {
            $this->error('Limit must be between 1 and 5000.');

            return self::INVALID;
        }
        $payments = Payment::where('status', 'pending')->where('reconciled_at', '<=', now()->subMinutes(5))
            ->whereBetween('created_at', [now()->subDays(3), now()->subMinute()])
            ->orderBy('reconciled_at')->orderBy('id')->limit($limit)->get(['id', 'reference']);
        foreach ($payments as $payment) {
            ReconcilePayment::dispatch($payment->reference);
        }
        $this->info($payments->count().' payment checks dispatched.');

        return self::SUCCESS;
    }
}
