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

    // Table context (from QR code)
    public ?int $tableId = null;
    public ?string $tableNumber = null;
    public ?string $qrCode = null;

    protected $listeners = ['createAddress'];

    public function mount(Request $request)
    {
        $this->store = $request->store;

        // Check for table context from QR code
        $this->tableId = session('current_table_id');
        $this->tableNumber = session('current_table_number');
        $this->qrCode = session('current_qr_code');
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
