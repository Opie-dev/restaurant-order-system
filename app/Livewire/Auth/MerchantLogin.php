<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;
use Illuminate\Http\Request;

#[Layout('layouts.auth')]
class MerchantLogin extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function authenticate(): void
    {
        $this->validate();

        // Find merchant (admin) user
        $user = User::where('email', $this->email)
            ->where('role', 'admin')
            ->first();

        if (!$user) {
            $this->addError('email', 'Invalid email or password.');
            return;
        }

        // Check if user is disabled
        if ($user->is_disabled) {
            $this->addError('email', 'Your account has been disabled. Please contact support.');
            return;
        }

        // Attempt authentication
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            request()->session()->regenerate();

            // Redirect to store selector for merchants
            $this->redirect(route('admin.stores.select'), navigate: true);
        } else {
            $this->addError('email', 'Invalid email or password.');
        }
    }

    public function render()
    {
        return view('livewire.auth.merchant-login');
    }
}
