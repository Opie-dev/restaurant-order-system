<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class Register extends Component
{
    #[Validate('required|string|min:2|max:255')]
    public string $name = '';

    public string $email = '';

    #[Validate('required|string|min:6')]
    public string $password = '';

    #[Validate('required|string|min:6|same:password')]
    public string $password_confirmation = '';

    public function rules(): array
    {
        $storeId = request()->store?->id;

        return [
            'name' => 'required|string|min:2|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(function ($q) use ($storeId) {
                    return $q->where('store_id', $storeId);
                }),
            ],
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:password',
        ];
    }

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'customer',
            'store_id' => request()->store?->id,
        ]);

        Auth::login($user);
        request()->session()->regenerate();
        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
