<?php

namespace App\Services;

use App\Models;

class BackofficeRegistry
{
    /** @return array<string, array<string, mixed>> */
    public function modules(): array
    {
        $text = fn (string $label, bool $required = true) => ['label' => $label, 'type' => 'text', 'required' => $required];
        $area = fn (string $label) => ['label' => $label, 'type' => 'textarea', 'required' => true];
        $select = fn (string $label, array $options) => ['label' => $label, 'type' => 'select', 'options' => $options, 'required' => true];
        $number = fn (string $label) => ['label' => $label, 'type' => 'number', 'required' => true];
        $date = fn (string $label) => ['label' => $label, 'type' => 'date', 'required' => true];
        $url = fn (string $label, bool $required = false) => ['label' => $label, 'type' => 'url', 'required' => $required];
        $status = $select('Status', ['draft', 'published', 'archived']);
        $module = fn (string $label, string $group, string $model, string $permission, array $columns, array $fields = [], array $extra = []) => [
            'label' => $label, 'group' => $group, 'model' => $model, 'permission' => $permission,
            'columns' => $columns, 'fields' => $fields, 'search' => $columns[0], ...$extra,
        ];
        $content = ['title' => $text('Judul'), 'slug' => $text('Slug'), 'excerpt' => $text('Ringkasan', false), 'body' => $area('Isi / deskripsi'), 'image_url' => $url('URL gambar'), 'status' => $status, 'published_at' => ['label' => 'Jadwal publikasi', 'type' => 'datetime-local', 'required' => false], 'position' => $number('Urutan')];
        $modules = [];
        foreach (['homepage' => 'Homepage', 'blog' => 'Blog & Cerita', 'destination' => 'Destinasi', 'hidden-gem' => 'Hidden Gems', 'culinary' => 'Kuliner', 'souvenir' => 'Oleh-oleh', 'page' => 'Halaman Statis'] as $key => $label) {
            $modules[$key] = $module($label, 'Content & CMS', Models\ContentPage::class, 'content', ['title', 'slug', 'status', 'published_at', 'position'], $content, ['scope' => ['type', $key]]);
        }
        return $modules + [
            'faqs' => $module('FAQ', 'Content & CMS', Models\Faq::class, 'content', ['question', 'category', 'status', 'position'], ['category' => $text('Kategori'), 'question' => $text('Pertanyaan'), 'answer' => $area('Jawaban'), 'status' => $status, 'position' => $number('Urutan')]),
            'partners' => $module('Kerja Sama & Partner', 'Content & CMS', Models\Partner::class, 'content', ['name', 'website_url', 'status', 'position'], ['name' => $text('Nama partner'), 'image_url' => $url('URL logo', true), 'website_url' => $url('Website'), 'status' => $status, 'position' => $number('Urutan')]),
            'media' => $module('Media Library', 'Content & CMS', Models\MediaAsset::class, 'content', ['name', 'mime_type', 'size', 'visibility']),
            'vendors' => $module('Vendor & Verifikasi', 'Operations', Models\Vendor::class, 'operations', ['name', 'city', 'status', 'email'], [], ['actions' => ['verify', 'reject', 'suspend'], 'detail' => ['phone', 'description', 'document_id', 'verification_note']]),
            'trips' => $module('Trip & Moderasi', 'Operations', Models\Trip::class, 'operations', ['title', 'destination', 'departure_date', 'price', 'status'], [], ['actions' => ['publish', 'reject', 'archive'], 'detail' => ['description', 'itinerary', 'meeting_point', 'capacity', 'reserved_seats', 'vendor_id']]),
            'bookings' => $module('Pemesanan', 'Operations', Models\Booking::class, 'operations', ['reference', 'contact_name', 'participants', 'total', 'status'], [], ['actions' => ['confirm', 'start', 'complete', 'cancel'], 'detail' => ['trip_id', 'vendor_id', 'contact_phone', 'expires_at']]),
            'support' => $module('Pusat Bantuan & Chat', 'Operations', Models\SupportTicket::class, 'operations', ['subject', 'category', 'priority', 'status'], ['status' => $select('Status', ['open', 'in_progress', 'resolved', 'closed']), 'priority' => $select('Prioritas', ['normal', 'high', 'urgent'])], ['create' => false]),
            'reviews' => $module('Rating & Ulasan', 'Operations', Models\Review::class, 'operations', ['body', 'rating', 'status'], ['status' => $select('Moderasi', ['published', 'hidden'])], ['create' => false]),
            'payments' => $module('Pembayaran', 'Finance', Models\Payment::class, 'finance', ['reference', 'amount', 'status', 'provider', 'paid_at']),
            'ledger' => $module('Ledger & Rekonsiliasi', 'Finance', Models\LedgerEntry::class, 'finance', ['reference', 'account', 'amount', 'description']),
            'refunds' => $module('Pengembalian Dana', 'Finance', Models\Refund::class, 'finance', ['reason', 'booking_id', 'amount', 'status'], [], ['actions' => ['approve', 'reject']]),
            'payouts' => $module('Pencairan Vendor', 'Finance', Models\Payout::class, 'finance', ['status', 'vendor_id', 'booking_id', 'amount'], [], ['actions' => ['approve']]),
            'promotions' => $module('Promo & Voucher', 'Growth', Models\Promotion::class, 'growth', ['name', 'code', 'type', 'value', 'used_count', 'status'], ['name' => $text('Nama promo'), 'code' => $text('Kode voucher'), 'type' => $select('Jenis diskon', ['fixed', 'percent']), 'value' => $number('Nilai diskon (rupiah / persen)'), 'minimum_amount' => $number('Minimum belanja (IDR)'), 'maximum_discount' => ['label' => 'Batas diskon (IDR)', 'type' => 'number', 'required' => false], 'usage_limit' => $number('Kuota penggunaan'), 'starts_at' => $date('Mulai'), 'ends_at' => $date('Berakhir'), 'status' => $status]),
            'campaigns' => $module('Campaign & Challenges', 'Growth', Models\Campaign::class, 'growth', ['name', 'type', 'starts_at', 'ends_at', 'status'], ['name' => $text('Nama campaign'), 'type' => $select('Jenis', ['campaign', 'challenge', 'mission']), 'description' => $area('Deskripsi & ketentuan'), 'starts_at' => $date('Mulai'), 'ends_at' => $date('Berakhir'), 'status' => $status]),
            'affiliates' => $module('Affiliate', 'Growth', Models\Affiliate::class, 'growth', ['code', 'user_id', 'commission_bps', 'status'], ['user_id' => $number('ID pengguna'), 'code' => $text('Kode referral'), 'commission_bps' => $number('Komisi basis points (100 = 1%)'), 'status' => $select('Status', ['pending', 'active', 'suspended'])]),
            'rewards' => $module('Ledger Points', 'Growth', Models\RewardEntry::class, 'growth', ['reference', 'user_id', 'points', 'description']),
            'audit' => $module('Audit Log', 'System', Models\AuditLog::class, 'system', ['action', 'entity_type', 'entity_id', 'user_id', 'created_at']),
            'settings' => $module('Pengaturan Platform', 'System', Models\PlatformSetting::class, 'system', ['key', 'value'], ['key' => $select('Pengaturan', ['support_email', 'support_phone', 'platform_name']), 'value' => $text('Nilai')]),
        ];
    }

    /** @return array<string, mixed> */
    public function get(string $module): array
    {
        return $this->modules()[$module] ?? abort(404);
    }

    /** @return array<int, array<string, string>> */
    public function navigation(Models\User $user): array
    {
        $items = [];
        foreach ($this->modules() as $key => $module) {
            if ($user->hasPermission($module['permission'].'.view')) {
                $items[] = ['key' => $key, 'label' => $module['label'], 'group' => $module['group'], 'url' => route('admin.resources.index', $key)];
            }
        }
        if ($user->hasPermission('users.view')) {
            $items[] = ['key' => 'users', 'label' => 'Pengguna & Admin', 'group' => 'System', 'url' => route('admin.access.users')];
        }
        if ($user->hasPermission('roles.view')) {
            $items[] = ['key' => 'roles', 'label' => 'Roles & Permissions', 'group' => 'System', 'url' => route('admin.access.roles')];
        }
        return $items;
    }
}

