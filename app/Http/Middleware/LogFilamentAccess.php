<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogFilamentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Filament Access Attempt', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'is_admin' => auth()->user()?->is_admin,
            'isAdmin_method' => auth()->check() && method_exists(auth()->user(), 'isAdmin') ? auth()->user()->isAdmin() : null,
            'canAccessPanel' => auth()->check() && method_exists(auth()->user(), 'canAccessPanel') ? 'method_exists' : 'method_missing',
        ]);

        $response = $next($request);

        Log::info('Filament Access Response', [
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'user_id' => auth()->id(),
        ]);

        return $response;
    }
}
