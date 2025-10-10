<div class="px-6 py-8">
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto">
            <div class="fixed top-0 left-0 right-0 z-10">
                @include('livewire.customer._baner')
            </div>

            <div class="mt-[16rem] lg:mt-[20rem]">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">My Addresses</h1>
                            <p class="text-gray-600">Manage your delivery addresses</p>
                        </div>
                        <a href="{{ route('menu.store.checkout', ['store' => $store->slug]) }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Checkout
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- List -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Saved addresses</h2>

                        @if($this->addresses->isEmpty())
                            <div class="text-gray-600 text-sm">No addresses yet.</div>
                        @else
                            <div class="space-y-3">
                                @foreach($this->addresses as $addr)
                                    <div x-data="{ open: false }" class="border rounded-lg">
                                        <!-- Summary row -->
                                        <button @click="open = !open" class="w-full p-4 flex items-center justify-between">
                                            <div class="flex items-center gap-3 text-left">
                                                <span class="font-medium text-gray-900 truncate max-w-[12rem] sm:max-w-none">{{ $addr->recipient_name }}</span>
                                                <span class="text-sm text-gray-600 hidden sm:inline">• {{ $addr->city }}</span>
                                                @if($addr->is_default)
                                                    <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">Default</span>
                                                @endif
                                            </div>
                                            <svg x-show="!open" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                            <svg x-show="open" class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        </button>

                                        <!-- Details -->
                                        <div x-show="open" x-transition class="px-4 pb-4">
                                            <div class="text-sm text-gray-700">{{ $addr->line1 }}@if($addr->line2), {{ $addr->line2 }}@endif</div>
                                            <div class="text-sm text-gray-700">{{ $addr->postal_code }} {{ $addr->city }}@if($addr->state), {{ $addr->state }}@endif, {{ $addr->country }}</div>
                                            @if($addr->phone)<div class="text-sm text-gray-600">{{ $addr->phone }}</div>@endif
                                            @if($addr->label)<div class="text-xs text-gray-500">Label: {{ $addr->label }}</div>@endif

                                            <div class="flex items-center gap-2 mt-3">
                                                <button wire:click="edit({{ $addr->id }})" class="px-3 py-1.5 text-sm border rounded-lg">Edit</button>
                                                @unless($addr->is_default)
                                                    <button wire:click="setDefault({{ $addr->id }})" class="px-3 py-1.5 text-sm border rounded-lg">Set default</button>
                                                @endunless
                                                <button wire:click="delete({{ $addr->id }})" class="px-3 py-1.5 text-sm border rounded-lg text-red-600">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Form -->
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ $editingId ? 'Edit address' : 'Add new address' }}</h2>
                        <form wire:submit.prevent="save" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Label (optional)</label>
                                    <input type="text" wire:model.blur="label" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                                    @error('label')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Recipient name</label>
                                    <input type="text" wire:model.blur="recipient_name" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                                    @error('recipient_name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" wire:model.blur="phone" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                                @error('phone')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address line 1</label>
                                <input type="text" wire:model.blur="line1" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                                @error('line1')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address line 2 (optional)</label>
                                <input type="text" wire:model.blur="line2" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                                @error('line2')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" wire:model.blur="city" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                                    @error('city')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">State</label>
                                    <input type="text" wire:model.blur="state" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                                    @error('state')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Postal code</label>
                                    <input type="text" wire:model.blur="postal_code" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                                    @error('postal_code')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                                <label class="inline-flex items-center gap-2 mt-6">
                                    <input type="checkbox" wire:model.live="is_default" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" />
                                    <span class="text-sm text-gray-700">Set as default</span>
                                </label>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                @if($editingId)
                                    <button type="button" wire:click="cancel" class="px-4 py-2 border rounded-lg">Cancel</button>
                                @endif
                                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg">Save address</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
