<?php

namespace App\Services\Admin;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StoreService
{
    /**
     * Get the current store for the authenticated admin user
     */
    public function getCurrentStore(): ?Store
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return null;
        }

        $storeId = Session::get('current_store_id');

        if (!$storeId) {
            return null;
        }

        return $user->stores()->find($storeId);
    }

    /**
     * Set the current store for the authenticated admin user
     */
    public function setCurrentStore(Store $store): void
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            throw new \Exception('Only admin users can set current store');
        }

        // Verify the store belongs to the user
        if (!$user->stores()->where('id', $store->id)->exists()) {
            throw new \Exception('Store does not belong to the user');
        }

        Session::put('current_store_id', $store->id);
    }

    /**
     * Clear the current store from session
     */
    public function clearCurrentStore(): void
    {
        Session::forget('current_store_id');
    }

    /**
     * Get all stores for the authenticated admin user
     */
    public function getUserStores()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return collect();
        }

        return $user->stores()->orderBy('name')->get();
    }

    /**
     * Check if user has any stores
     */
    public function userHasStores(): bool
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return false;
        }

        return $user->stores()->count() > 0;
    }
}
