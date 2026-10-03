<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorApplicationRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorApplicationController extends Controller
{
    public function store(StoreVendorApplicationRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $data = $request->safe()->except('password');
            $user = User::create([
                'name' => $data['name'], 'email' => $data['email'],
                'password' => $request->validated('password'), 'city' => $data['city'],
            ]);
            Vendor::create([
                'user_id' => $user->id, 'name' => $data['business'], 'email' => $data['email'],
                'phone' => $data['phone'], 'city' => $data['city'], 'description' => $data['notes'] ?? null, 'status' => 'pending',
            ]);
            $application = VendorApplication::create([...$data, 'user_id' => $user->id, 'contact' => $data['email'], 'status' => 'pending']);
            User::where('status', 'active')->whereHas('roles.permissions', fn ($query) => $query->where('name', 'vendor.verify'))
                ->with('roles.permissions')->each(function (User $admin) use ($application): void {
                    if (! $admin->hasPermission('admin.access') || ! $admin->hasPermission('operations.view')) {
                        return;
                    }
                    $admin->notifications()->create([
                        'id' => (string) Str::uuid7(), 'type' => 'vendor.application',
                        'data' => ['title' => 'Pengajuan mitra baru', 'reference' => $application->business,
                            'url' => route('admin.panel.resources.show', ['panel' => 'operations', 'module' => 'vendor-applications', 'record' => $application->id], false)],
                    ]);
                });
        });

        return back()->with('success', 'Pengajuan berhasil dikirim ke admin pengelola vendor. Tim kami akan menghubungi kontak yang Anda cantumkan.');
    }
}
