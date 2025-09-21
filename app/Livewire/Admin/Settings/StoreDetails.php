<?php

namespace App\Livewire\Admin\Settings;

use App\Models\StoreSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.admin')]
class StoreDetails extends Component
{
    #[Validate('required|string|max:255')]
    public string $store_name = '';

    #[Validate('nullable|string|max:500')]
    public ?string $description = null;

    #[Validate('required|string|max:255')]
    public string $address_line1 = '';

    #[Validate('nullable|string|max:255')]
    public ?string $address_line2 = null;

    #[Validate('required|string|max:100')]
    public string $city = '';

    #[Validate('required|string|max:100')]
    public string $state = '';

    #[Validate('required|string|max:20')]
    public string $postal_code = '';

    #[Validate('required|string|max:20|regex:/^[0-9+\-\s()]+$/')]
    public string $phone = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('nullable|image|max:2048')]
    public $logo;

    public ?string $logo_path = null;

    public function mount()
    {
        // Load existing store details from database
        $settings = StoreSetting::getSettings();

        if ($settings) {
            $this->store_name = $settings->store_name;
            $this->description = $settings->description;
            $this->address_line1 = $settings->address_line1;
            $this->address_line2 = $settings->address_line2;
            $this->city = $settings->city;
            $this->state = $settings->state;
            $this->postal_code = $settings->postal_code;
            $this->phone = $settings->phone;
            $this->email = $settings->email;
            $this->logo_path = $settings->logo_path;
        } else {
            // Default values if no settings exist
            $this->store_name = 'Restaurant Admin';
            $this->description = 'Your local restaurant serving delicious meals';
            $this->address_line1 = '123 Main Street';
            $this->address_line2 = 'Suite 100';
            $this->city = 'Kuala Lumpur';
            $this->state = 'Wilayah Persekutuan';
            $this->postal_code = '50000';
            $this->phone = '+60 12-345-6789';
            $this->email = 'info@restaurant.com';
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'store_name' => $this->store_name,
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

        // Save to database
        StoreSetting::updateSettings($data);

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
