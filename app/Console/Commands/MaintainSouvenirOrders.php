<?php

namespace App\Console\Commands;

use App\Jobs\ReconcileSouvenirPayment;
use App\Models\SouvenirOrder;
use App\Models\SouvenirPayment;
use App\Services\SouvenirCommerceService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class MaintainSouvenirOrders extends Command
{
    protected $signature = 'souvenirs:maintain {--limit=100}';

    protected $description = 'Release expired souvenir reservations and queue bounded Midtrans reconciliation';

    public function handle(SouvenirCommerceService $service): int
    {
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 1000]]);
        if ($limit === false) {
            $this->error('Limit must be between 1 and 1000.');

            return self::INVALID;
        }
        foreach (SouvenirOrder::where('status', 'awaiting_payment')->where('expires_at', '<=', now())->orderBy('id')->limit($limit)->get() as $order) {
            try {
                $service->transition($order, 'expired');
            } catch (ValidationException) {
                continue;
            }
        }
        if (config('platform.midtrans_server_key')) {
            foreach (SouvenirPayment::whereIn('status', ['pending', 'cancelled', 'expired'])->where('reconciled_at', '<=', now()->subMinutes(5))->where('created_at', '>=', now()->subDays(3))->orderBy('reconciled_at')->limit($limit)->get(['reference']) as $payment) {
                ReconcileSouvenirPayment::dispatch($payment->reference);
            }
        }

        return self::SUCCESS;
    }
}
