<?php

namespace App\Http\Middleware;

use App\Services\AdminPanelService;
use App\Services\BackofficeRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveAdminPanel
{
    public function __construct(private AdminPanelService $panels, private BackofficeRegistry $registry) {}

    public function handle(Request $request, Closure $next): Response
    {
        $module = $request->route('module');
        $expected = $module ? $this->panels->forModule($module, $this->registry) : ($request->routeIs('admin.tours.*') ? 'content' : 'super');
        $panel = $request->route('panel') ?? $expected;
        $definition = $this->panels->definitions()[$panel] ?? abort(404);
        abort_unless($request->user()->hasPermission($definition['permission']), 403);
        if ($module) {
            $moduleDef = $this->registry->get($module);
            $canAccess = $expected === $panel || $panel === 'super' || $request->user()->hasPermission($moduleDef['permission'].'.view');
            abort_unless($canAccess, 404);
        }
        $request->attributes->set('admin_panel', $panel);
        $request->route()->forgetParameter('panel');

        return $next($request);
    }
}
