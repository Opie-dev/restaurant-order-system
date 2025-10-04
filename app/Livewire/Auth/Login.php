<?php

namespace App\Livewire\Auth;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
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

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $user = Auth::user();

            // Check if user is disabled
            if ($user->is_disabled) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();

                $this->addError('email', "Your account has been disabled.");
                return;
            }

            request()->session()->regenerate();

            if ($user->role === 'admin') {
                $this->redirect(route('admin.stores.select'), navigate: true);
            } else {
                $this->redirect(route('menu.store.index', ['store' => $this->store->slug]), navigate: true);
            }
        } else {

            $randomSixString = Str::random(6);

            $user = User::create([
                'name' => $this->email,
                'email' => $this->email,
                'password' => Hash::make($randomSixString),
                'role' => 'customer',
            ]);

            Auth::login($user);
            request()->session()->regenerate();
            $this->redirect(route('menu.store.index', ['store' => $this->store->slug]), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
