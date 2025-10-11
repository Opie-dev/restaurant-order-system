<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Store;
use App\Services\Admin\StoreService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class StoreAddress extends Component
{
    public ?Store $currentStore = null;
    private $storeService;

    public ?string $address_line1 = '';
    public ?string $address_line2 = null;
    public ?string $city = '';
    public ?string $state = '';
    public ?string $postal_code = '';

    public function boot(): void
    {
        $this->storeService = app(StoreService::class);
    }

    public function mount(): void
    {
        $this->currentStore = $this->storeService->getCurrentStore();
        if (!$this->currentStore) {
            $this->redirectRoute('admin.stores.select');
            return;
        }
        $this->address_line1 = $this->currentStore->address_line1 ?? '';
        $this->address_line2 = $this->currentStore->address_line2;
        $this->city = $this->currentStore->city ?? '';
        $this->state = $this->currentStore->state ?? '';
        $this->postal_code = $this->currentStore->postal_code ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        $this->currentStore->update([
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
        ]);

        $this->dispatch('flash', type: 'success', message: 'Store address updated.');
    }

    public function render()
    {
        return view('livewire.admin.settings.store-address');
    }
}
