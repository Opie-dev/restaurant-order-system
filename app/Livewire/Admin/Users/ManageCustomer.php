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

    protected $listeners = ['createAddress'];

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

    public function createAddress($addressData)
    {
        // If setting as default, unset other default addresses
        if ($addressData['is_default']) {
            $this->customer->addresses()->update(['is_default' => false]);
        }

        $this->customer->addresses()->create($addressData);
        $this->dispatch('flash', type: 'success', message: 'Address added successfully.');
    }

    public function setDefaultAddress(UserAddress $address)
    {
        // Unset all other default addresses
        $this->customer->addresses()->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        $this->dispatch('flash', type: 'success', message: 'Default address updated successfully.');
    }

    public function deleteAddress(UserAddress $address)
    {
        $address->delete();
        $this->dispatch('flash', type: 'success', message: 'Address deleted successfully.');
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

        $this->dispatch('flash', type: 'success', message: "Customer has been {$status} successfully and has been notified via email.");
    }

    public function getAddressesProperty()
    {
        return $this->customer->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.admin.users.manage-customer');
    }
}
