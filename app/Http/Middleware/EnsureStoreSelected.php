<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only apply to admin users
        if ($user && $user->role === 'admin') {
            // Check if user has stores
            if ($user->stores()->count() === 0) {
                return redirect()->route('admin.stores.create')
                    ->with('error', 'You need to create a store first.');
            }

            // Check if current store is selected
            $currentStoreId = session('current_store_id');
            if (!$currentStoreId) {
                // If user has only one store, auto-select it
                if ($user->stores()->count() === 1) {
                    $store = $user->stores()->first();
                    session(['current_store_id' => $store->id]);
                } else {
                    // Redirect to store selection
                    return redirect()->route('admin.stores.select');
                }
            } else {
                // Verify the selected store belongs to the user
                $store = $user->stores()->find($currentStoreId);
                if (!$store) {
                    session()->forget('current_store_id');
                    return redirect()->route('admin.stores.select');
                }
            }
        }

        return $next($request);
    }
}
