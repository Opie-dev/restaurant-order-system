<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.admin')]
class CreateUser extends Component
{
    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('required|email:rfc,dns|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:6')]
    public string $password = '';

    // Address fields
    #[Validate('nullable|string|max:50')]
    public ?string $label = null;
    #[Validate('required|string|max:120')]
    public string $recipient_name = '';
    #[Validate('required|string|max:30')]
    public ?string $phone = null;
    #[Validate('required|string|max:255')]
    public string $line1 = '';
    #[Validate('nullable|string|max:255')]
    public ?string $line2 = null;
    #[Validate('required|string|max:120')]
    public string $city = '';
    #[Validate('nullable|string|max:120')]
    public ?string $state = null;
    #[Validate('required|string|max:20')]
    public string $postal_code = '';
    #[Validate('required|string|max:2')]
    public string $country = 'MY';

    public function save(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'customer',
        ]);

        $user->addresses()->create([
            'label' => $this->label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_default' => true,
        ]);

        session()->flash('success', 'Customer created');
        $this->redirectRoute('admin.customers.index');
    }

    public function render()
    {
        return view('livewire.admin.users.create-user');
    }
}
