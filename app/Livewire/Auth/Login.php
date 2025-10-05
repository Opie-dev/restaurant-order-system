<?php

namespace App\Livewire\Auth;

use App\Models\Store;
use App\Mail\NewCustomerRegistrationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

#[Layout('layouts.auth')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:6')]
    public string $password = '';

    public bool $remember = false;
    public ?Store $store = null;

    public function mount(Request $request)
    {
        $this->store = $request->store;
    }

    public function authenticate(): void
    {
        $this->validate();

        $user = User::where('email', $this->email)
            ->where('role', 'customer')
            ->where('store_id', $this->store->id)
            ->first();

        if (!$user) {
            $randomSixString = Str::random(6);
            $user = User::create([
                'name' => $this->email,
                'email' => $this->email,
                'password' => Hash::make($randomSixString),
                'role' => 'customer',
                'store_id' => $this->store->id,
                'is_disabled' => false,
            ]);

            // Send notification email to store admin for new customer registration
            try {
                if ($this->store->admin && $this->store->admin->email) {
                    Mail::to($this->store->admin->email)->send(new NewCustomerRegistrationNotification($user, $this->store));
                }
            } catch (\Exception $emailException) {
                // Log email error but don't fail the registration
                Log::warning('Failed to send new customer registration notification email', [
                    'user_id' => $user->id,
                    'store_id' => $this->store->id,
                    'admin_email' => $this->store->admin?->email,
                    'error' => $emailException->getMessage()
                ]);
            }
        }

        if (!$user->store_id) {
            $user->store_id = $this->store->id;
            $user->save();
        }

        if ($user->is_disabled) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            $this->addError('email', "Your account has been disabled.");
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            request()->session()->regenerate();
            if ($user->role === 'admin') {
                $this->redirect(route('admin.stores.select'), navigate: true);
            } else {
                $this->redirect(route('menu.store.index', ['store' => $this->store->slug]), navigate: true);
            }
        }

        $this->addError('email', "Invalid email or password.");
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
