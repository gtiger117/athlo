<?php

namespace App\Http\Middleware;

use Closure;

class AllowLocalhost
{
    public function handle($request, Closure $next)
    {
        $allowed_ips = ['127.0.0.1', '::1', '82.165.77.39', '88.208.212.173', '88.208.212.173', '77.68.102.29'];
        // Check if the request is coming from localhost
        if (in_array($request->server('REMOTE_ADDR'),$allowed_ips) === true) {
            return $next($request);
        }

        // Return error response for non-localhost requests
        return response()->json(['message' => 'Access denied.'], 403);
    }
}
