<?php

namespace App\Livewire\Customer;

use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Store;
use Illuminate\Http\Request;

#[Layout('layouts.customer')]
class Addresses extends Component
{
    public ?Store $store = null;

    protected $listeners = ['createAddress'];

    public function mount(Request $request)
    {
        $this->store = $request->store;
    }

    public function createAddress($addressData)
    {
        $user = Auth::user();

        // If setting as default, unset other default addresses
        if ($addressData['is_default']) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create($addressData);
        $this->dispatch('flash', type: 'success', message: 'Address added successfully.');
    }

    public function setDefault(int $id): void
    {
        $user = Auth::user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->where('id', $id)->update(['is_default' => true]);
        $this->dispatch('flash', type: 'success', message: 'Default address updated');
    }

    public function delete(int $id): void
    {
        $addr = Auth::user()->addresses()->findOrFail($id);
        $addr->delete();
        $this->dispatch('flash', type: 'success', message: 'Address removed');
    }

    public function getAddressesProperty()
    {
        return Auth::user()->addresses()->orderByDesc('is_default')->latest()->get();
    }

    public function render()
    {
        return view('livewire.customer.addresses');
    }
}
