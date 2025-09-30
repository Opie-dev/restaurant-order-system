<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Store;
use App\Services\StoreService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.admin')]
class StoreDetails extends Component
{
    use WithFileUploads;
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255|unique:stores,slug')]
    public string $slug = '';

    #[Validate('nullable|string|max:500')]
    public ?string $description = null;

    #[Validate('required|string|max:255')]
    public ?string $address_line1 = '';

    #[Validate('nullable|string|max:255')]
    public ?string $address_line2 = null;

    #[Validate('required|string|max:100')]
    public ?string $city = '';

    #[Validate('required|string|max:100')]
    public ?string $state = '';

    #[Validate('required|string|max:20')]
    public ?string $postal_code = '';

    #[Validate('required|string|max:20|regex:/^[0-9+\-\s()]+$/')]
    public ?string $phone = '';

    #[Validate('required|email')]
    public ?string $email = '';

    #[Validate('nullable|image|max:2048')]
    public $logo;

    public ?string $logo_path = null;
    public ?Store $currentStore = null;

    public function mount()
    {
        // Load current store from StoreService
        $storeService = app(StoreService::class);
        $this->currentStore = $storeService->getCurrentStore();

        if ($this->currentStore) {
            $this->name = $this->currentStore->name ?? '';
            $this->slug = $this->currentStore->slug ?? '';
            $this->description = $this->currentStore->description;
            $this->address_line1 = $this->currentStore->address_line1 ?? '';
            $this->address_line2 = $this->currentStore->address_line2;
            $this->city = $this->currentStore->city ?? '';
            $this->state = $this->currentStore->state ?? '';
            $this->postal_code = $this->currentStore->postal_code ?? '';
            $this->phone = $this->currentStore->phone ?? '';
            $this->email = $this->currentStore->email ?? '';
            $this->logo_path = $this->currentStore->logo_path;
        } else {
            // Redirect to store selection if no store is selected
            $this->redirectRoute('admin.stores.select');
        }
    }

    public function save()
    {
        // Update validation to ignore current store's slug
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stores,slug,' . $this->currentStore->id,
            'description' => 'nullable|string|max:500',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'email' => 'required|email|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        if (!$this->currentStore) {
            session()->flash('error', 'No store selected.');
            return;
        }

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
        ];

        // Handle logo upload
        if ($this->logo) {
            // Delete old logo if exists
            if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
                Storage::disk('public')->delete($this->logo_path);
            }

            // Store new logo
            $logoPath = $this->logo->store('logos', 'public');
            $data['logo_path'] = $logoPath;
            $this->logo_path = $logoPath;
        }

        // Update the current store
        $this->currentStore->update($data);

        // Clear the logo upload after successful save
        $this->logo = null;

        session()->flash('success', 'Store details updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.store-details', [
            'navigationBar' => true,
            'showBackButton' => true,
            'pageTitle' => 'Store Details',
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => '#'],
                ['label' => 'Store Details']
            ],
            'actionButtons' => [
                [
                    'type' => 'button',
                    'label' => 'Save Changes',
                    'onclick' => '$wire.save()',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                ]
            ]
        ]);
    }
}
