<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\SouvenirOrder;
use App\Models\SupportMessage;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;

class OrderNotificationObserver
{
    public function created(Booking|SouvenirOrder|SupportMessage $order): void
    {
        if ($order instanceof SupportMessage) {
            $ticket = $order->supportTicket;
            $vendorUserId = $ticket->vendor_id ? Vendor::whereKey($ticket->vendor_id)->value('user_id') : null;
            foreach (array_unique(array_filter([$ticket->user_id, $vendorUserId])) as $userId) {
                if ($userId !== $order->user_id) {
                    User::find($userId)?->notifications()->create([
                        'id' => (string) Str::uuid7(), 'type' => 'support.message',
                        'data' => ['title' => 'Pesan baru', 'reference' => $ticket->subject, 'url' => route('support.show', $ticket, false)],
                    ]);
                }
            }

            return;
        }
        $this->notify($order);
    }

    public function updated(Booking|SouvenirOrder|SupportMessage $order): void
    {
        if (! $order instanceof SupportMessage && $order->wasChanged('status')) {
            $this->notify($order);
        }
    }

    private function notify(Booking|SouvenirOrder $order): void
    {
        $isTrip = $order instanceof Booking;
        $labels = ['awaiting_payment' => 'Menunggu pembayaran', 'paid' => 'Pembayaran terverifikasi', 'confirmed' => 'Trip dikonfirmasi', 'ongoing' => 'Trip berlangsung', 'processing' => 'Pesanan disiapkan', 'ready_for_pickup' => 'Siap diambil', 'shipped' => 'Pesanan dikirim', 'completed' => 'Pesanan selesai', 'cancelled' => 'Pesanan dibatalkan', 'expired' => 'Batas pembayaran berakhir', 'refunded' => 'Pembayaran dikembalikan'];
        $title = $labels[$order->status] ?? 'Status pesanan diperbarui';
        $recipients = [[$order->user_id, $isTrip ? route('bookings.show', $order, false) : route('souvenirs.orders.show', $order, false)]];
        $vendorUserId = $order->vendor()->value('user_id');
        if ($vendorUserId && $vendorUserId !== $order->user_id) {
            $recipients[] = [$vendorUserId, $isTrip ? route('vendor.section', 'bookings', false) : route('vendor.souvenirs', [], false)];
        }
        foreach ($recipients as [$userId, $url]) {
            User::find($userId)?->notifications()->create([
                'id' => (string) Str::uuid7(),
                'type' => 'order.status',
                'data' => ['title' => $title, 'reference' => $order->reference, 'url' => $url, 'status' => $order->status],
            ]);
        }
    }
}
