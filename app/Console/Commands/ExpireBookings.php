<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class ExpireBookings extends Command
{
    protected $signature = 'bookings:expire';

    protected $description = 'Release inventory held by unpaid expired bookings';

    public function handle(BookingService $service): int
    {
        Booking::where('status', 'awaiting_payment')->where('expires_at', '<=', now())->chunkById(100, function ($bookings) use ($service) {
            foreach ($bookings as $booking) {
                try {
                    $service->transition($booking, 'expired');
                } catch (ValidationException) {
                    continue;
                }
            }
        });

        return self::SUCCESS;
    }
}
