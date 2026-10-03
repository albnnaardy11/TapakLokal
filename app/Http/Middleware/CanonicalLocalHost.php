<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanonicalLocalHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = rtrim((string) config('app.url'), '/');
        $host = parse_url($origin, PHP_URL_HOST);
        if (app()->environment('local')
            && in_array($request->method(), ['GET', 'HEAD'], true)
            && in_array($request->getHost(), ['127.0.0.1', 'localhost'], true)
            && in_array($host, ['127.0.0.1', 'localhost'], true)
            && $request->getSchemeAndHttpHost() !== $origin) {
            return redirect()->away($origin.$request->getRequestUri());
        }

        return $next($request);
    }
}
