<?php

namespace App\Services;

use App\Models\User;

class AdminPanelService
{
    /** @return array<string, array{label: string, permission: string, description: string}> */
    public function definitions(): array
    {
        return [
            'super' => ['label' => 'Super Admin', 'permission' => 'system.view', 'description' => 'Laporan lintas divisi, audit aktivitas, dan pengendalian akses.'],
            'content' => ['label' => 'Content Admin', 'permission' => 'content.view', 'description' => 'Kelola publikasi, destinasi, media, dan pengalaman virtual 360°.'],
            'operations' => ['label' => 'Operations Admin', 'permission' => 'operations.view', 'description' => 'Tinjau mitra, paket, pemesanan, dan layanan pelanggan.'],
            'finance' => ['label' => 'Finance Admin', 'permission' => 'finance.view', 'description' => 'Pantau pembayaran, rekonsiliasi, refund, dan pencairan.'],
            'growth' => ['label' => 'Growth Admin', 'permission' => 'growth.view', 'description' => 'Kelola promosi, kampanye, affiliate, dan reward.'],
        ];
    }

    /** @return array<int, array<string, string>> */
    public function available(User $user): array
    {
        if (! $user->hasPermission('admin.access')) {
            return [];
        }
        $panels = [];
        foreach ($this->definitions() as $key => $definition) {
            if ($user->hasPermission($definition['permission'])) {
                $panels[] = ['key' => $key, ...$definition, 'url' => route('admin.panel.dashboard', ['panel' => $key])];
            }
        }

        return $panels;
    }

    public function home(User $user): string
    {
        return $this->available($user)[0]['key'] ?? abort(403);
    }

    public function forModule(string $module, BackofficeRegistry $registry): string
    {
        return match ($registry->get($module)['permission']) {
            'system' => 'super',
            'content' => 'content',
            'operations' => 'operations',
            'finance' => 'finance',
            'growth' => 'growth',
            default => abort(404),
        };
    }
}
