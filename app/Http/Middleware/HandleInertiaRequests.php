<?php

namespace App\Http\Middleware;

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
            'auth' => fn () => $request->user() ? [
                'user' => $request->user()->only(['id', 'name', 'email', 'phone', 'city']),
                'permissions' => $request->user()->loadMissing('roles.permissions')->roles->flatMap->permissions->pluck('name')->unique()->values(),
            ] : ['user' => null, 'permissions' => []],
            'flash' => ['success' => fn () => $request->session()->get('success'), 'error' => fn () => $request->session()->get('error')],
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
