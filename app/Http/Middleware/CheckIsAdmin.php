<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('CheckIsAdmin middleware executed', [
            'url' => $request->fullUrl(),
            'authenticated' => auth()->check(),
            'user_id' => auth()->check() ? auth()->user()->id : null,
            'user_email' => auth()->check() ? auth()->user()->email : null,
            'is_admin_field' => auth()->check() ? auth()->user()->is_admin : null,
            'isAdmin_method' => auth()->check() ? auth()->user()->isAdmin() : null,
        ]);

        if (!auth()->check()) {
            Log::warning('CheckIsAdmin: User not authenticated');
            abort(403, 'Unauthorized. Admin access required.');
        }

        if (!auth()->user()->isAdmin()) {
            Log::warning('CheckIsAdmin: User is not admin', [
                'user_id' => auth()->user()->id,
                'email' => auth()->user()->email,
                'is_admin' => auth()->user()->is_admin,
            ]);
            abort(403, 'Unauthorized. Admin access required.');
        }

        Log::info('CheckIsAdmin: Access granted', [
            'user_id' => auth()->user()->id,
            'email' => auth()->user()->email,
        ]);

        return $next($request);
    }
}
