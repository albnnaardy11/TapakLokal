<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\FinanceService;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ReconcilePayment implements ShouldBeUniqueUntilProcessing, ShouldQueue
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

    public function handle(FinanceService $finance): void
    {
        $payment = Payment::where('reference', $this->reference)->firstOrFail();
        $key = config('platform.midtrans_server_key');
        if (! $key) {
            $this->fail(new RuntimeException('Payment gateway is not configured.'));

            return;
        }
        $response = Http::withBasicAuth($key, '')->acceptJson()->connectTimeout(5)->timeout(15)
            ->get($finance->apiBase().'/v2/'.rawurlencode($this->reference).'/status');
        if ($response->status() === 404 || (string) $response->json('status_code') === '404') {
            $payment->update(['reconciled_at' => now()]);

            return;
        }
        if (! $response->successful() || $response->json('order_id') !== $this->reference || ! is_string($response->json('transaction_id')) || ! is_string($response->json('transaction_status'))) {
            throw new RuntimeException('Payment status could not be verified.');
        }
        try {
            $finance->applyStatus($response->json());
        } catch (ValidationException $exception) {
            $this->fail($exception);

            return;
        }
        $payment->update(['reconciled_at' => now()]);
    }
}
