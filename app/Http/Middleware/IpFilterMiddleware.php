<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpFilterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = Config::get('ip-filter.allowed_ips');
        $requestIp = $request->ip();

        if (in_array($requestIp, $allowedIps)) {
            return $next($request);
        }

        return abort(403, 'Unauthorized IP address.');
    }
}
