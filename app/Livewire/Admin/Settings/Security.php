<?php

namespace App\Livewire\Admin\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.admin')]
class Security extends Component
{
    #[Validate('required|string|min:8')]
    public string $current_password = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $new_password = '';

    #[Validate('required|string|min:8')]
    public string $new_password_confirmation = '';

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
