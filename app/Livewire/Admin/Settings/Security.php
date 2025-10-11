<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Services\Admin\StoreService;
use Illuminate\Http\Request;

#[Layout('layouts.admin')]
class Security extends Component
{
    #[Validate('required|string|min:8')]
    public string $current_password = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $new_password = '';

    #[Validate('required|string|min:8')]
    public string $new_password_confirmation = '';

    // Store deactivation properties
    #[Validate('required|string|min:8')]
    public string $store_deactivation_password = '';

    // Account deletion properties
    #[Validate('required|string|min:8')]
    public string $account_deletion_password = '';

    #[Validate('required|string')]
    public string $account_deletion_confirmation = '';

    private $storeService;

    public function boot(): void
    {
        $this->storeService = new StoreService();
    }

    public function updatePassword()
    {
        $this->validate();

        // Verify current password
        if (!Hash::check($this->current_password, Auth::user()->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        // Update password
        Auth::user()->update([
            'password' => Hash::make($this->new_password)
        ]);

        // Clear form
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        // Show success message
        $this->dispatch('flash', type: 'success', message: 'Password updated successfully. You will be logged out for security.');

        // Logout and redirect to login
        Auth::logout();

        return redirect()->route('merchant.login')->with('message', 'Password updated successfully. Please login with your new password.');
    }

    public function deactivateStore()
    {
        $this->validate([
            'store_deactivation_password' => 'required|string|min:8'
        ]);

        // Verify password
        if (!Hash::check($this->store_deactivation_password, Auth::user()->password)) {
            $this->addError('store_deactivation_password', 'The password is incorrect.');
            return;
        }

        // Get the current user's store
        $store = $this->storeService->getCurrentStore();

        if (!$store) {
            $this->addError('store_deactivation_password', 'No store found for this account.');
            return;
        }

        // Deactivate the store
        $store->update(['is_active' => false]);

        // Clear form
        $this->reset(['store_deactivation_password']);

        // Show success message
        $this->dispatch('flash', type: 'success', message: 'Store has been deactivated successfully. Your store is no longer visible to customers.');
    }

    public function deleteAccount(Request $request)
    {
        $this->validate([
            'account_deletion_password' => 'required|string|min:8',
            'account_deletion_confirmation' => 'required|string|in:DELETE'
        ]);

        // Verify password
        if (!Hash::check($this->account_deletion_password, Auth::user()->password)) {
            $this->addError('account_deletion_password', 'The password is incorrect.');
            return;
        }

        // Verify confirmation text
        if ($this->account_deletion_confirmation !== 'DELETE') {
            $this->addError('account_deletion_confirmation', 'Please type DELETE to confirm account deletion.');
            return;
        }

        DB::transaction(function () {
            $user = Auth::user();

            // Delete all user-owned store data and related content
            $stores = $user->stores;

            foreach ($stores as $store) {
                // Delete categories
                $store->categories()->each(function ($category) {
                    $category->delete();
                });

                // Delete menu items
                $store->menuItems()->each(function ($item) {
                    $item->delete();
                });

                // Delete orders and their items/payments
                $store->orders()->each(function ($order) {
                    $order->items()->delete();
                    $order->payments()->delete();
                    $order->delete();
                });

                // Delete customers that only belong to this store (optional, clarify business rule if re-used elsewhere)
                if (method_exists($store, 'customers')) {
                    $store->customers()->each(function ($customer) {
                        // Only delete customer if they belong exclusively to this store
                        if (method_exists($customer, 'stores') && $customer->stores()->count() === 1) {
                            $customer->delete();
                        }
                    });
                }

                // Delete addresses (if related by hasMany)
                if (method_exists($store, 'addresses')) {
                    $store->addresses()->delete();
                }

                // Delete store settings
                if (method_exists($store, 'settings')) {
                    $store->settings()->delete();
                }

                // Delete daily menu availabilities
                if (method_exists($store, 'dailyMenuAvailabilities')) {
                    $store->dailyMenuAvailabilities()->delete();
                }

                // Delete/clear store media (if implemented)
                if (method_exists($store, 'clearMedia')) {
                    $store->clearMedia();
                }

                // Finally, delete the store itself
                $store->delete();
            }

            // Soft delete the user account
            $user->delete();
        });

        // Logout and redirect
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('merchant.login')->with('message', 'Your account and all associated data have been permanently deleted.');
    }

    public function render()
    {
        return view('livewire.admin.settings.security');
    }
}
