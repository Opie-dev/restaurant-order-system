<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow menu routes to work without session store (they set it themselves)
        if ($request->routeIs('menu.store') && !session('current_store_id')) {
            return $next($request);
        }

        if (!session('current_store_id')) {
            return redirect()->route('stores.index');
        }

        return $next($request);
    }
}
