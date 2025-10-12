<div class="px-6 py-8">
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto">
            <div class="fixed top-0 left-0 right-0 z-10">
                @include('livewire.customer._baner')
            </div>

            <div class="mt-[10rem] lg:mt-[20rem]">
                <div class="mb-6">
                    <div class="flex items-center justify-between gap-2">
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

                <!-- Address List -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Saved addresses</h2>
                        <button wire:click="$dispatch('showAddAddress')" 
                                class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Address
                        </button>
                    </div>

                    @if($this->addresses->isEmpty())
                        <div class="text-gray-600 text-sm">No addresses yet.</div>
                    @else
                        <div class="space-y-3">
                            @foreach($this->addresses as $addr)
                                @php $loopIndex = $loop->index; @endphp
                                <div 
                                    x-data="{ open: {{ $loopIndex === 0 ? 'true' : 'false' }} }" 
                                    class="border rounded-lg"
                                >
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

                                        <div class="flex md:flex-row flex-col items-center gap-2 mt-3">
                                            <button wire:click="$dispatch('showEditAddress', { addressId: {{ $addr->id }} })" 
                                                    class="w-full md:w-auto px-3 py-1.5 text-sm border rounded-lg">Edit</button>
                                            @unless($addr->is_default)
                                                <button wire:click="setDefault({{ $addr->id }})" 
                                                        class="w-full md:w-auto px-3 py-1.5 text-sm border rounded-lg">Set default</button>
                                            @endunless
                                            <button wire:click="delete({{ $addr->id }})" 
                                                    wire:confirm="Are you sure you want to delete this address?"
                                                    class="w-full md:w-auto px-3 py-1.5 text-sm border rounded-lg text-red-600">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Address Form Component -->
                <livewire:shared.address-form />
            </div>
        </div>
    </div>
</div>
