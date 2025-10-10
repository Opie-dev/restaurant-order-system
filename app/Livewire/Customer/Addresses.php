<?php

namespace App\Livewire\Customer;

use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Store;
use App\Services\StoreService;
use Illuminate\Http\Request;

#[Layout('layouts.customer')]
class Addresses extends Component
{
    public ?int $editingId = null;

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

    public bool $is_default = false;
    public ?Store $store = null;

    public function mount(Request $request)
    {
        $this->store = $request->store;
    }

    public function edit(int $id): void
    {
        $addr = Auth::user()->addresses()->findOrFail($id);
        $this->editingId = $addr->id;
        $this->fill($addr->only(['label', 'recipient_name', 'phone', 'line1', 'line2', 'city', 'state', 'postal_code', 'is_default']));
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();
        $data = $this->only(['label', 'recipient_name', 'phone', 'line1', 'line2', 'city', 'state', 'postal_code']);

        if ($this->editingId) {
            $address = $user->addresses()->findOrFail($this->editingId);
            $address->update($data);
        } else {
            $address = $user->addresses()->create($data);

            // Auto-assign as default if this is the user's first address
            $totalAddresses = $user->addresses()->count();
            if ($totalAddresses === 1) {
                $this->is_default = true;
            }
        }

        if ($this->is_default) {
            $user->addresses()->update(['is_default' => false]);
            $address->is_default = true;
            $address->save();
        }

        $this->dispatch('flash', ['type' => 'success', 'message' => 'Address saved']);
        $this->resetForm();
    }

    public function setDefault(int $id): void
    {
        $user = Auth::user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->where('id', $id)->update(['is_default' => true]);
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Default address updated']);
    }

    public function delete(int $id): void
    {
        $addr = Auth::user()->addresses()->findOrFail($id);
        $addr->delete();
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Address removed']);
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'label', 'recipient_name', 'phone', 'line1', 'line2', 'city', 'state', 'postal_code', 'is_default']);
        $this->is_default = false;
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
