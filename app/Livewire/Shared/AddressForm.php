<?php

namespace App\Livewire\Shared;

use App\Models\UserAddress;
use Livewire\Component;
use App\Constants\CountryCodes;

class AddressForm extends Component
{
    // Form state
    public bool $isEditing = false;
    public ?UserAddress $editingAddress = null;
    public bool $showForm = false;

    // Address properties
    public string $addressLabel = '';
    public string $recipientName = '';
    public string $phone = '';
    public string $line1 = '';
    public string $line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postalCode = '';
    public string $country_code = '';

    public array $countryCodes = [];
    public bool $isDefault = false;

    // Validation rules
    protected function rules(): array
    {
        return [
            'addressLabel' => 'required|string|max:255',
            'recipientName' => 'required|string|max:255',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postalCode' => 'required|string|max:20',
            'country_code' => 'required|string',
            'phone' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $error = CountryCodes::getPhoneNumberValidationError($this->country_code, (string)$value);
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
        ];
    }

    // Events to emit
    protected $listeners = ['showAddAddress', 'showEditAddress', 'hideForm'];

    public function mount()
    {
        $this->countryCodes = CountryCodes::getAll();
        $this->country_code = CountryCodes::getByCode('+60')['code'];
    }

    public function showAddAddress()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->editingAddress = null;
        $this->showForm = true;
    }

    public function showEditAddress($addressId)
    {
        $this->editingAddress = UserAddress::findOrFail($addressId);
        $this->addressLabel = $this->editingAddress->label;
        $this->recipientName = $this->editingAddress->recipient_name;

        // Handle phone number - if it contains country code, extract just the number part
        $this->phone = $this->editingAddress->phone ?? '';
        $this->country_code = $this->editingAddress->country_code ?? CountryCodes::getByCode('+60')['code'];
        $this->line1 = $this->editingAddress->line1;
        $this->line2 = $this->editingAddress->line2 ?? '';
        $this->city = $this->editingAddress->city;
        $this->state = $this->editingAddress->state;
        $this->postalCode = $this->editingAddress->postal_code;
        $this->isDefault = $this->editingAddress->is_default;

        $this->isEditing = true;
        $this->showForm = true;
    }

    public function hideForm()
    {
        $this->resetForm();
        $this->showForm = false;
        $this->isEditing = false;
        $this->editingAddress = null;
    }

    public function saveAddress()
    {
        $this->validate();

        $addressData = [
            'label' => $this->addressLabel,
            'recipient_name' => $this->recipientName,
            'phone' => $this->phone,
            'country_code' => $this->country_code ?? CountryCodes::getByCode('+60')['code'],
            'line1' => $this->line1,
            'line2' => $this->line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'is_default' => $this->isDefault,
        ];

        if ($this->isEditing && $this->editingAddress) {
            // Update existing address
            $this->editingAddress->update($addressData);
            $message = 'Address updated successfully.';
        } else {
            // Create new address - emit event to parent component
            $this->dispatch('createAddress', $addressData);
            $message = 'Address added successfully.';
        }

        $this->hideForm();
        $this->dispatch('flash', type: 'success', message: $message);
    }

    public function cancelForm()
    {
        $this->hideForm();
    }

    private function resetForm()
    {
        $this->addressLabel = '';
        $this->recipientName = '';
        $this->phone = '';
        $this->line1 = '';
        $this->line2 = '';
        $this->city = '';
        $this->state = '';
        $this->postalCode = '';
        $this->country_code = CountryCodes::getByCode('+60')['code'];
        $this->isDefault = false;
    }

    public function render()
    {
        return view('livewire.shared.address-form');
    }
}
