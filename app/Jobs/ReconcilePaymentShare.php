<?php

namespace App\Jobs;

use App\Models\PaymentShare;
use App\Services\SplitPaymentService;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReconcilePaymentShare implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Queueable;

    public int $tries = 6;

    public int $timeout = 25;

    public int $uniqueFor = 1800;

    public function __construct(public string $reference)
    {
        $this->onConnection(config('platform.payment_queue_connection'));
    }

    public function uniqueId(): string
    {
        return $this->reference;
    }

    /** @return array<int, int> */
    public function backoff(): array
    {
        return [10, 30, 60, 180, 300];
    }

    public function handle(SplitPaymentService $service): void
    {
        $service->check(PaymentShare::where('reference', $this->reference)->firstOrFail());
    }
}
