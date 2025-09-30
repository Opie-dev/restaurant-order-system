<?php

namespace App\Livewire\Admin\Stores;

use App\Models\Store;
use App\Services\StoreService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.auth')]
class StoreSelector extends Component
{
    use WithFileUploads;

    public $stores;
    public $selectedStoreId;

    // Create store form fields
    public $name = '';
    public $slug = '';
    public $description = '';
    public $address_line1 = '';
    public $address_line2 = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $phone = '';
    public $email = '';
    public $logo;
    public $showCreateForm = false;

    public function mount()
    {
        $storeService = app(StoreService::class);
        $this->stores = $storeService->getUserStores();

        // Check if store is provided in URL
        $storeId = request('store');
        if ($storeId) {
            $store = $this->stores->find($storeId);
            if ($store) {
                $storeService->setCurrentStore($store);
                $storeName = $store->name;
                return redirect()->route('admin.dashboard')->with('success', "Switched to " . $storeName);
            }
        }

        $currentStore = $storeService->getCurrentStore();
        $this->selectedStoreId = $currentStore?->id ?? null;
    }

    public function selectStore()
    {
        $this->validate([
            'selectedStoreId' => 'required|exists:stores,id'
        ]);

        $store = $this->stores->find($this->selectedStoreId);

        if ($store) {
            $storeService = app(StoreService::class);
            $storeService->setCurrentStore($store);

            $this->dispatch('store-selected', storeId: $store->id);

            return redirect()->route('admin.dashboard')
                ->with('success', "Switched to {$store->name}");
        }
    }

    public function createStore()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stores,slug|regex:/^[a-z0-9\-]+$/',
            'description' => 'nullable|string|max:1000',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'email' => 'required|email|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'admin_id' => Auth::id(),
            'is_active' => true,
        ];

        // Handle logo upload
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        $store = Store::create($data);

        // Refresh stores list
        $storeService = app(StoreService::class);
        $this->stores = $storeService->getUserStores();

        // Reset form
        $this->reset([
            'name',
            'slug',
            'description',
            'address_line1',
            'address_line2',
            'city',
            'state',
            'postal_code',
            'phone',
            'email',
            'logo',
            'showCreateForm'
        ]);

        session()->flash('success', 'Store created successfully!');
    }

    public function deleteStore($storeId)
    {
        $store = $this->stores->find($storeId);

        if ($store) {
            $store->delete();

            // Refresh stores list
            $storeService = app(StoreService::class);
            $this->stores = $storeService->getUserStores();

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
