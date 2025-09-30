<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not authenticated, allow access (guest customers)
        if (!Auth::check()) {
            return $next($request);
        }

        // If user is authenticated and is admin, redirect to admin dashboard
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admins cannot access customer pages. Please use the admin panel.');
        }

        // If user is customer or guest, allow access
        return $next($request);
    }
}
