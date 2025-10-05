<?php

namespace App\Livewire\Admin\Stores;

use App\Models\Store;
use App\Services\Admin\StoreService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.auth')]
class CreateStore extends Component
{
    use WithFileUploads;

    // Store form fields
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

    public function mount()
    {
        // Auto-generate slug from name when name changes
        // This is handled by the updatedName() method
    }

    public function updatedName()
    {
        $this->slug = Str::slug($this->name);
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
            'is_onboarding' => true,
        ];

        // Handle logo upload
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        $store = Store::create($data);

        session()->flash('success', 'Store created successfully!');

        // Redirect back to store selector
        return redirect()->route('admin.stores.select');
    }

    public function render()
    {
        return view('livewire.admin.stores.create-store');
    }
}
