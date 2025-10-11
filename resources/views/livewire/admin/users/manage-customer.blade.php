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

    <div class="text-right mb-4">
        <button 
            wire:click="toggleCustomerStatus"
            wire:confirm="{{ $customer->is_disabled ? 'Are you sure you want to enable this customer?' : 'Are you sure you want to disable this customer?' }}"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md {{ $customer->is_disabled ? 'text-green-700 bg-green-100 hover:bg-green-200' : 'text-red-700 bg-red-100 hover:bg-red-200' }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $customer->is_disabled ? 'focus:ring-green-500' : 'focus:ring-red-500' }} transition-colors"
        >
            @if($customer->is_disabled)
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Enable Customer
            @else
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 12.728 9 9 0 0112.728-12.728z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l-6 6"></path>
                </svg>
                Disable Customer
            @endif
        </button>
    </div>
    <!-- Customer Info -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Customer Information</h2>
            <div class="flex items-center space-x-3">
                @if($customer->is_disabled)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Disabled
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Active
                    </span>
                @endif
            </div>
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
                <div class="flex item-center space-x-2">
                    <a href="{{ route('admin.orders.index', ['user' => $customer->id]) }}" 
                        class="text-blue-600 hover:text-blue-700 text-sm font-medium" 
                        title="View orders">
                        {{ $customer->orders()->count() }}
                    </a>
                </div>
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
            <button wire:click="$dispatch('showAddAddress')" 
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
                                    <div>
                                        {{ $address->line1 }}
                                        @if($address->line2), {{ $address->line2 }}@endif,
                                        {{ $address->postal_code }} {{ $address->city }}, {{ $address->state }}, {{ $address->country }}
                                    </div>
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
                                <button wire:click="$dispatch('showEditAddress', { addressId: {{ $address->id }} })" 
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

    <!-- Address Form Component -->
    <livewire:shared.address-form />
</div>
