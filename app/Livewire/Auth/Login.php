<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function authenticate(): void
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $user = Auth::user();

            // Check if user is disabled
            if ($user->is_disabled) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                // Get store settings for the message
                $storeSettings = \App\Models\StoreSetting::getSettings();
                $storeName = $storeSettings?->store_name ?? 'our store';
                $storePhone = $storeSettings?->phone ?? 'our support team';

                $this->addError('email', "Your account has been disabled by {$storeName}. Please contact {$storePhone} for support.");
                return;
            }

            request()->session()->regenerate();

            if ($user->role === 'admin') {
                $this->redirect(route('admin.menu.index'), navigate: true);
            } else {
                $this->redirect(route('menu'), navigate: true);
            }
            return;
        }

        $this->addError('email', 'Invalid credentials.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
