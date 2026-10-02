<?php

namespace App\Http\Middleware;

use App\Models\SouvenirCartItem;
use App\Models\SupportTicket;
use App\Services\AdminPanelService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'navigation' => fn () => $request->user() ? [
                'cartCount' => SouvenirCartItem::where('user_id', $request->user()->id)->count(),
                'unreadCount' => $request->user()->unreadNotifications()->count(),
                'notifications' => $request->user()->notifications()->latest()->orderByDesc('id')->limit(5)->get(['id', 'data', 'read_at', 'created_at']),
                'messages' => SupportTicket::query()
                    ->when(! ($request->user()->hasPermission('admin.access') && $request->user()->hasPermission('operations.manage')), fn ($query) => $query->where(function ($scope) use ($request) {
                        $scope->where('user_id', $request->user()->id);
                        if ($request->user()->hasPermission('vendor.access') && $request->user()->vendor) {
                            $scope->orWhere('vendor_id', $request->user()->vendor->id);
                        }
                    }))
                    ->whereIn('status', ['open', 'in_progress'])->latest('id')->limit(5)->get(['id', 'subject', 'status', 'updated_at'])
                    ->map(fn ($ticket) => [
                        'id' => $ticket->id, 'sender' => $ticket->subject, 'snippet' => $ticket->status === 'open' ? 'Tiket bantuan terbuka' : 'Sedang ditangani',
                        'time' => $ticket->updated_at->toIso8601String(),
                        'url' => route('support.show', $ticket),
                    ]),
            ] : ['cartCount' => 0, 'unreadCount' => 0, 'notifications' => []],
            'auth' => fn () => $request->user() ? [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'username' => $request->user()->username,
                    'phone' => $request->user()->phone,
                    'city' => $request->user()->city,
                    'avatar' => $request->user()->avatar,
                    'points' => $request->user()->points,
                    'tier' => $request->user()->tier,
                ],
                'permissions' => $request->user()->loadMissing('roles.permissions')->roles->flatMap->permissions->pluck('name')->unique()->values(),
            ] : ['user' => null, 'permissions' => []],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'uploaded_media' => fn () => $request->session()->get('uploaded_media'),
                'login_success_data' => fn () => $request->session()->get('login_success_data'),
            ],
            'adminPanel' => function () use ($request): ?array {
                $key = $request->attributes->get('admin_panel');
                if (! $key || ! $request->user()) {
                    return null;
                }
                $panels = app(AdminPanelService::class);

                return ['key' => $key, ...$panels->definitions()[$key], 'url' => route('admin.panel.dashboard', ['panel' => $key]), 'available' => $panels->available($request->user())];
            },
        ];
    }
}
