<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request)
    {
        if (!$request->expectsJson()) {
            // Check if we're in a store context
            if ($request->route('store')) {
                return route('menu.store.login', ['store' => $request->route('store')]);
            }

            // Fallback to home page if no store context
            return route('home');
        }
    }
}
