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
        session()->flash('success', 'Password updated successfully. You will be logged out for security.');

        // Logout and redirect to login
        Auth::logout();

        return redirect()->route('login')->with('message', 'Password updated successfully. Please login with your new password.');
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
        session()->flash('success', 'Store has been deactivated successfully. Your store is no longer visible to customers.');
    }

    public function deleteAccount()
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

            // Get user's stores
            $stores = $user->stores;

            // Soft delete all stores (this will cascade to related data)
            foreach ($stores as $store) {
                $store->delete();
            }

            // Soft delete the user account
            $user->delete();
        });

        // Logout and redirect
        Auth::logout();

        return redirect()->route('welcome')->with('message', 'Your account and all associated data have been permanently deleted.');
    }

    public function render()
    {
        return view('livewire.admin.settings.security', [
            'navigationBar' => true,
            'showBackButton' => false,
            'pageTitle' => 'Security Settings',
            'breadcrumbs' => [
                ['name' => 'Settings', 'url' => '#'],
                ['name' => 'Security', 'url' => '#'],
            ],
        ]);
    }
}
