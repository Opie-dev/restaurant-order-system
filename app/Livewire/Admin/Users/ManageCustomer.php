<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\StoreSetting;
use App\Mail\PasswordChangedNotification;
use App\Mail\CustomerStatusChangedNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

#[Layout('layouts.admin')]
class ManageCustomer extends Component
{
    public User $customer;

    // Password change
    #[Validate('required|string|min:8')]
    public string $newPassword = '';

    #[Validate('required|string|same:newPassword')]
    public string $confirmPassword = '';

    // Address management
    public bool $showAddAddress = false;
    public bool $showEditAddress = false;
    public ?UserAddress $editingAddress = null;

    #[Validate('required|string|max:255')]
    public string $addressLabel = '';

    #[Validate('required|string|max:255')]
    public string $recipientName = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('required|string|max:255')]
    public string $line1 = '';

    #[Validate('nullable|string|max:255')]
    public string $line2 = '';

    #[Validate('required|string|max:100')]
    public string $city = '';

    #[Validate('required|string|max:100')]
    public string $state = '';

    #[Validate('required|string|max:20')]
    public string $postalCode = '';

    #[Validate('required|string|max:100')]
    public string $country = 'Malaysia';

    public bool $isDefault = false;

    public function mount(User $customer)
    {
        $this->customer = $customer;
    }

    public function changePassword()
    {
        $this->validate([
            'newPassword' => 'required|string|min:8',
            'confirmPassword' => 'required|string|same:newPassword'
        ]);

        $this->customer->update([
            'password' => Hash::make($this->newPassword)
        ]);

        // Send email notification to customer
        Mail::to($this->customer->email)->send(new PasswordChangedNotification($this->customer));

        $this->newPassword = '';
        $this->confirmPassword = '';

        session()->flash('success', 'Password updated successfully and customer has been notified via email.');
    }

    public function showAddAddressForm()
    {
        $this->resetAddressForm();
        $this->showAddAddress = true;
        $this->showEditAddress = false;
    }

    public function showEditAddressForm(UserAddress $address)
    {
        $this->editingAddress = $address;
        $this->addressLabel = $address->label;
        $this->recipientName = $address->recipient_name;
        $this->phone = $address->phone ?? '';
        $this->line1 = $address->line1;
        $this->line2 = $address->line2 ?? '';
        $this->city = $address->city;
        $this->state = $address->state;
        $this->postalCode = $address->postal_code;
        $this->country = $address->country;
        $this->isDefault = $address->is_default;

        $this->showEditAddress = true;
        $this->showAddAddress = false;
    }

    public function saveAddress()
    {
        $this->validate([
            'addressLabel' => 'required|string|max:255',
            'recipientName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postalCode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        // If setting as default, unset other default addresses
        if ($this->isDefault) {
            $this->customer->addresses()->update(['is_default' => false]);
        }

        if ($this->editingAddress) {
            // Update existing address
            $this->editingAddress->update([
                'label' => $this->addressLabel,
                'recipient_name' => $this->recipientName,
                'phone' => $this->phone,
                'line1' => $this->line1,
                'line2' => $this->line2,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postalCode,
                'country' => $this->country,
                'is_default' => $this->isDefault,
            ]);

            $message = 'Address updated successfully.';
        } else {
            // Create new address
            $this->customer->addresses()->create([
                'label' => $this->addressLabel,
                'recipient_name' => $this->recipientName,
                'phone' => $this->phone,
                'line1' => $this->line1,
                'line2' => $this->line2,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postalCode,
                'country' => $this->country,
                'is_default' => $this->isDefault,
            ]);

            $message = 'Address added successfully.';
        }

        $this->resetAddressForm();
        $this->showAddAddress = false;
        $this->showEditAddress = false;
        $this->editingAddress = null;

        session()->flash('success', $message);
    }

    public function setDefaultAddress(UserAddress $address)
    {
        // Unset all other default addresses
        $this->customer->addresses()->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        session()->flash('success', 'Default address updated successfully.');
    }

    public function deleteAddress(UserAddress $address)
    {
        $address->delete();
        session()->flash('success', 'Address deleted successfully.');
    }

    public function toggleCustomerStatus()
    {
        $oldStatus = $this->customer->is_disabled;

        $this->customer->update([
            'is_disabled' => !$this->customer->is_disabled
        ]);

        $newStatus = $this->customer->is_disabled;
        $status = $newStatus ? 'disabled' : 'enabled';

        // Get store settings for email
        $storeSettings = StoreSetting::getSettings();
        $storeName = $storeSettings?->store_name ?? 'Our Store';
        $storePhone = $storeSettings?->phone ?? 'our support team';

        // Send email notification to customer
        Mail::to($this->customer->email)->send(
            new CustomerStatusChangedNotification(
                $this->customer,
                $newStatus,
                $storeName,
                $storePhone
            )
        );

        session()->flash('success', "Customer has been {$status} successfully and has been notified via email.");
    }

    public function cancelAddressForm()
    {
        $this->resetAddressForm();
        $this->showAddAddress = false;
        $this->showEditAddress = false;
        $this->editingAddress = null;
    }

    private function resetAddressForm()
    {
        $this->addressLabel = '';
        $this->recipientName = '';
        $this->phone = '';
        $this->line1 = '';
        $this->line2 = '';
        $this->city = '';
        $this->state = '';
        $this->postalCode = '';
        $this->country = 'Malaysia';
        $this->isDefault = false;
    }

    public function getAddressesProperty()
    {
        return $this->customer->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.admin.users.manage-customer', [
            'navigationBar' => true,
            'showBackButton' => true,
            'pageTitle' => 'Manage Customer: ' . $this->customer->name,
            'breadcrumbs' => [
                ['label' => 'Customers', 'url' => route('admin.customers.index')],
                ['label' => 'Manage Customer']
            ]
        ]);
    }
}
