<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class MerchantRegister extends Component
{
    #[Validate('required|string|min:2|max:255')]
    public string $name = '';

    #[Validate('required|email|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:6')]
    public string $password = '';

    #[Validate('required|string|min:6|same:password')]
    public string $password_confirmation = '';

    #[Validate('required|string|min:10|max:15')]
    public string $phone = '';

    public function register(): void
    {
        $this->validate();

        try {
            // Create the merchant user
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'admin',
                'is_disabled' => false,
            ]);

            // Log the registration
            Log::info('New merchant registered', [
                'user_id' => $user->id,
                'email' => $user->email,
                'phone' => $this->phone
            ]);

            // Login the user
            Auth::login($user);
            request()->session()->regenerate();

            // Redirect to store selector
            $this->redirect(route('admin.stores.select'), navigate: true);
        } catch (\Exception $e) {
            Log::error('Merchant registration failed', [
                'email' => $this->email,
                'error' => $e->getMessage()
            ]);

            $this->addError('email', 'Registration failed. Please try again or contact support.');
        }
    }

    public function render()
    {
        return view('livewire.auth.merchant-register');
    }
}
