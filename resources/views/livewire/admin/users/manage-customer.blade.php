<div class="w-full px-6 py-8">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Customer Info -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Customer Information</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <p class="text-sm text-gray-900">{{ $customer->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <p class="text-sm text-gray-900">{{ $customer->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Member Since</label>
                <p class="text-sm text-gray-900">{{ $customer->created_at->format('M j, Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Orders</label>
                <p class="text-sm text-gray-900">{{ $customer->orders()->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Password Change -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Change Password</h2>
        <form wire:submit.prevent="changePassword" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" 
                           wire:model="newPassword" 
                           id="newPassword"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Enter new password">
                    @error('newPassword')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" 
                           wire:model="confirmPassword" 
                           id="confirmPassword"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Confirm new password">
                    @error('confirmPassword')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> The customer will receive an email notification when their password is updated.
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    <!-- Address Management -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Address Management</h2>
            <button wire:click="showAddAddressForm" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Add Address
            </button>
        </div>

        <!-- Address List -->
        @if($this->addresses->count() > 0)
            <div class="space-y-4 mb-6">
                @foreach($this->addresses as $address)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $address->is_default ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="font-medium text-gray-900">{{ $address->label }}</h3>
                                    @if($address->is_default)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Default
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-700">
                                    <div class="font-medium">{{ $address->recipient_name }}</div>
                                    <div>{{ $address->line1 }}@if($address->line2), {{ $address->line2 }}@endif</div>
                                    <div>{{ $address->postal_code }} {{ $address->city }}, {{ $address->state }}, {{ $address->country }}</div>
                                    @if($address->phone)
                                        <div class="text-gray-600">{{ $address->phone }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if(!$address->is_default)
                                    <button wire:click="setDefaultAddress({{ $address->id }})" 
                                            class="text-sm text-green-600 hover:text-green-700">
                                        Set Default
                                    </button>
                                @endif
                                <button wire:click="showEditAddressForm({{ $address->id }})" 
                                        class="text-sm text-blue-600 hover:text-blue-700">
                                    Edit
                                </button>
                                <button wire:click="deleteAddress({{ $address->id }})" 
                                        wire:confirm="Are you sure you want to delete this address?"
                                        class="text-sm text-red-600 hover:text-red-700">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>No addresses found for this customer.</p>
            </div>
        @endif

        <!-- Add/Edit Address Form -->
        @if($showAddAddress || $showEditAddress)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $showEditAddress ? 'Edit Address' : 'Add New Address' }}
                </h3>
                <form wire:submit.prevent="saveAddress" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="addressLabel" class="block text-sm font-medium text-gray-700 mb-1">Label</label>
                            <input type="text" 
                                   wire:model="addressLabel" 
                                   id="addressLabel"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="e.g., Home, Office">
                            @error('addressLabel')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label for="recipientName" class="block text-sm font-medium text-gray-700 mb-1">Recipient Name</label>
                            <input type="text" 
                                   wire:model="recipientName" 
                                   id="recipientName"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Full name">
                            @error('recipientName')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" 
                                   wire:model="phone" 
                                   id="phone"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Phone number">
                            @error('phone')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   wire:model="isDefault" 
                                   id="isDefault"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="isDefault" class="ml-2 block text-sm text-gray-900">
                                Set as default address
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="line1" class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                        <input type="text" 
                               wire:model="line1" 
                               id="line1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Street address, building number">
                        @error('line1')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="line2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                        <input type="text" 
                               wire:model="line2" 
                               id="line2"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Apartment, suite, unit, building, floor, etc.">
                        @error('line2')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" 
                                   wire:model="city" 
                                   id="city"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="City">
                            @error('city')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" 
                                   wire:model="state" 
                                   id="state"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="State">
                            @error('state')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label for="postalCode" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" 
                                   wire:model="postalCode" 
                                   id="postalCode"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Postal code">
                            @error('postalCode')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" 
                               wire:model="country" 
                               id="country"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Country">
                        @error('country')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                wire:click="cancelAddressForm"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            {{ $showEditAddress ? 'Update Address' : 'Add Address' }}
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
