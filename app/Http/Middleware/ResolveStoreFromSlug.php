<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Store;

class ResolveStoreFromSlug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the route has a store parameter
        if ($request->route('store:slug')) {
            $store = Store::where('slug', $request->route('store:slug'))->first();
            if (!$store) {
                return abort(404);
            }

            $request->merge(['store' => $store]);
        }

        return $next($request);
    }
}
