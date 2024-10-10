<?php

namespace App\Http\Middleware\API;

use App\Http\Controllers\API\Compro\ResponseFormatter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if not authenticated, return 401
        if (!auth('api')->check()) {
            return ResponseFormatter::error('Unauthorized', 401);
        }

        return $next($request);
    }
}
