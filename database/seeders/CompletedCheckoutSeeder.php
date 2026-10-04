<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Payment;
use App\Models\Review;
use App\Models\TravelerProfile;
use App\Models\Trip;
use App\Models\User;
use App\Services\AccessService;
use App\Services\BookingService;
use App\Services\FinanceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CompletedCheckoutSeeder extends Seeder
{
    private const BOOKING_KEY = '7ac51c30-5f5c-4de6-bd5d-a9543ae55bc9';

    public function run(AccessService $access, BookingService $bookings, FinanceService $finance): void
    {
        $access->seed();
        $traveler = $this->traveler($access);
        $this->call(BrenggoTripSeeder::class);
        $vendorTrip = Trip::query()
            ->where('slug', 'brenggo-bromo-sunrise-open-trip')
            ->where('status', 'published')
            ->firstOrFail();
        $trip = $this->completedTrip($vendorTrip);
        $profile = TravelerProfile::firstOrCreate(
            ['user_id' => $traveler->id, 'phone' => '081234567891'],
            [
                'name' => $traveler->name,
                'birth_date' => '1995-08-17',
                'emergency_contact' => 'Keluarga Demo · 081234567892',
            ],
        );
        $booking = Booking::where('idempotency_key', self::BOOKING_KEY)->first();

        if (! $booking) {
            $booking = $bookings->create($traveler, [
                'trip_id' => $trip->id,
                'participants' => 2,
                'contact_name' => $traveler->name,
                'contact_email' => $traveler->email,
                'contact_phone' => '081234567891',
                'traveler_details' => [
                    ['name' => $traveler->name, 'profile_id' => $profile->id],
                    ['name' => 'Raka Pratama', 'profile_id' => null],
                ],
                'special_request' => 'Data checkout selesai untuk memeriksa alur akun traveler, vendor, dan admin.',
                'idempotency_key' => self::BOOKING_KEY,
            ]);
        }

        $payment = Payment::where('booking_id', $booking->id)->firstOrFail();
        if ($payment->status === 'pending' && $booking->fresh()->status === 'awaiting_payment') {
            $payment->update(['method' => 'gopay', 'provider' => 'midtrans']);
            $finance->applyStatus([
                'order_id' => $payment->reference,
                'gross_amount' => (string) $payment->amount,
                'transaction_status' => 'settlement',
                'transaction_id' => 'seed-'.Str::lower((string) Str::ulid()),
            ]);
        }

        if (in_array($booking->fresh()->status, ['paid', 'confirmed', 'ongoing'], true)) {
            $trip->update([
                'departure_date' => today()->subDays(3),
                'end_date' => today()->subDays(2),
            ]);
            foreach (['confirmed', 'ongoing', 'completed'] as $status) {
                $current = $booking->fresh();
                if ($current->status !== $status) {
                    $booking = $bookings->transition($current, $status);
                }
            }
        }

        Favorite::firstOrCreate(['user_id' => $traveler->id, 'trip_id' => $trip->id]);
        Review::firstOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => $traveler->id,
                'trip_id' => $trip->id,
                'rating' => 5,
                'body' => 'Perjalanan tertata rapi, pemandu responsif, dan informasi keberangkatan sangat jelas.',
                'status' => 'published',
            ],
        );

        Cache::forget('public-content:version');
        $this->command?->info('Checkout selesai siap diuji: '.$traveler->email.' | Booking: '.$booking->reference.' | Trip: '.$trip->title);
        $this->command?->info('Data terlihat di akun traveler, booking vendor, payout finance admin, dan ledger points growth admin.');
    }

    private function traveler(AccessService $access): User
    {
        $email = (string) env('SEED_TRAVELER_EMAIL', 'traveler.checkout@tapaklokal.test');
        $password = (string) env('SEED_TRAVELER_PASSWORD');
        $existing = User::where('email', $email)->first();

        if (! $existing && ! app()->environment(['local', 'testing']) && ($password === '' || Str::endsWith($email, '.test'))) {
            throw new \RuntimeException('Production membutuhkan SEED_TRAVELER_EMAIL dan SEED_TRAVELER_PASSWORD untuk membuat akun checkout.');
        }

        $traveler = $existing ?? User::create([
            'name' => 'Traveler Checkout',
            'email' => $email,
            'password' => $password ?: 'TravelerCheckout2026!',
            'phone' => '081234567891',
            'city' => 'Jakarta',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $access->grant($traveler, 'traveler');

        return $traveler;
    }

    private function completedTrip(Trip $source): Trip
    {
        return Trip::firstOrCreate(
            ['slug' => 'checkout-selesai-bromo-demo'],
            [
                'vendor_id' => $source->vendor_id,
                'title' => 'Open Trip Bromo Sunrise — Riwayat Checkout',
                'type' => 'open-trip',
                'destination' => $source->destination,
                'description' => $source->description,
                'itinerary' => $source->itinerary,
                'meeting_point' => $source->meeting_point,
                'image_url' => $source->image_url,
                'departure_date' => today()->addDay(),
                'end_date' => today()->addDays(2),
                'capacity' => 8,
                'reserved_seats' => 0,
                'price' => $source->price,
                'status' => 'published',
                'experience' => $source->experience,
            ],
        );
    }
}
