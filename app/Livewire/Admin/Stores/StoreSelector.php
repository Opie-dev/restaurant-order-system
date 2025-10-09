<?php

namespace App\Livewire\Admin\Stores;

use App\Models\Store;
use App\Services\Admin\StoreService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class StoreSelector extends Component
{

    public ?Store $stores;
    public $selectedStoreId;
    private $storeService;


    public function boot()
    {
        $this->storeService = new StoreService();
    }

    public function mount()
    {
        $this->stores = $this->storeService->getUserStores();

        // Check if store is provided in URL
        $storeId = request('store');
        if ($storeId) {
            $store = $this->stores->find($storeId);
            if ($store) {
                $this->storeService->setCurrentStore($store);
                $storeName = $store->name;
                return redirect()->route('admin.dashboard')->with('success', "Switched to " . $storeName);
            }
        }

        $currentStore = $this->storeService->getCurrentStore();
        $this->selectedStoreId = $currentStore?->id ?? null;
    }

    public function selectStore()
    {
        $this->validate([
            'selectedStoreId' => 'required|exists:stores,id'
        ]);

        $store = $this->stores->find($this->selectedStoreId);

        if ($store) {
            $this->storeService->setCurrentStore($store);

            $this->dispatch('store-selected', storeId: $store->id);

            return redirect()->route('admin.dashboard')
                ->with('success', "Switched to {$store->name}");
        }
    }

    public function deleteStore($storeId)
    {
        $store = $this->stores->find($storeId);

        if ($store) {
            $store->delete();

            // Refresh stores list
            $this->stores = $this->storeService->getUserStores();

            // Clear selection if deleted store was selected
            if ($this->selectedStoreId == $storeId) {
                $this->selectedStoreId = null;
            }

            session()->flash('success', 'Store deleted successfully!');
        }
    }

    public function render()
    {
        return view('livewire.admin.stores.store-selector');
    }
}
