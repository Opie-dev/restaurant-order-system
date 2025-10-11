<div>
    @if($showForm)
        <div class="fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full z-50" x-data="{ show: @entangle('showForm') }">
            <div class="relative top-20 mx-auto p-5 border w-full md:w-1/2 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $isEditing ? 'Edit Address' : 'Add New Address' }}
                        </h3>
                        <button wire:click="hideForm" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form wire:submit.prevent="saveAddress" class="space-y-4">
                        <!-- Label and Recipient Name -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Label (optional)</label>
                                <input type="text" 
                                       wire:model="addressLabel" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                       placeholder="Home, Office, etc.">
                                @error('addressLabel')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Recipient name</label>
                                <input type="text" 
                                       wire:model="recipientName" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                       placeholder="Full name">
                                @error('recipientName')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="w-full">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <div class="flex w-full" x-data="phoneInput()">
                                <!-- Country Code Dropdown -->
                                <div class="relative">
                                    <button type="button" 
                                            @click="toggleDropdown()" 
                                            class="flex items-center justify-between w-32 px-3 py-3 border-2 border-gray-300 rounded-l-lg focus:border-purple-500 focus:ring-purple-500 bg-white hover:bg-gray-50">
                                        <span class="text-sm font-medium" x-text="selectedCountry.label"></span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div x-show="isOpen" 
                                            @click.away="closeDropdown()" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute z-50 mt-1 w-80 bg-white border border-gray-300 rounded-lg shadow-lg  overflow-y-auto max-h-60">
                                        
                                        <!-- Search Input -->
                                        <div class="p-3 border-b border-gray-200">
                                            <input type="text" 
                                                    x-model="searchQuery" 
                                                    @input="filterCountries()"
                                                    placeholder="Search country..." 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                        </div>
                                        
                                        <!-- Countries List -->
                                        <div class="px-3 py-2.5 overflow-y-auto">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                        @click="selectCountry(country)"
                                                        class="w-full px-4 py-2 text-left hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-sm font-medium" x-text="country.label"></span>
                                                        <span class="text-xs text-gray-500" x-text="country.country"></span>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                     <!-- Phone Number Input -->
                                     <input type="tel" 
                                            wire:model="phone" 
                                            class="flex-1 rounded-r-lg border-2 border-gray-300 border-l-0 w-full px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" 
                                            placeholder="Enter phone number" />
                            </div>
                            @error('phone')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>
                    
                        <!-- Address Line 1 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address line 1</label>
                            <input type="text" 
                                   wire:model="line1" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                   placeholder="Street address">
                            @error('line1')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <!-- Address Line 2 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address line 2 (optional)</label>
                            <input type="text" 
                                   wire:model="line2" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                   placeholder="Apartment, suite, etc.">
                            @error('line2')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                        </div>

                        <!-- City, State, Postal Code -->
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" 
                                       wire:model="city" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                       placeholder="City">
                                @error('city')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <input type="text" 
                                       wire:model="state" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                       placeholder="State">
                                @error('state')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal code</label>
                                <input type="text" 
                                       wire:model="postalCode" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500" 
                                       placeholder="Postal code">
                                @error('postalCode')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Set as Default Checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   wire:model="isDefault" 
                                   id="isDefault" 
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="isDefault" class="ml-2 block text-sm text-gray-900">
                                Set as default
                            </label>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 pt-4">
                            <button type="button" 
                                    wire:click="cancelForm"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2" wire:loading.attr="disabled" wire:target="saveTax">
                                <svg wire:loading wire:target="saveAddress" class="animate-spin -ml-1 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4z"/>
                                </svg>
                                <span>Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
         function phoneInput() {
             return {
                 isOpen: false,
                 searchQuery: '',
                 selectedCountry: {
                     code: '+60',
                     label: 'MY (+60)',
                     country: 'Malaysia'
                 },
                 countries: @json($this->countryCodes),
                 filteredCountries: @json($this->countryCodes),
                
                init() {
                    // Set default country based on current country_code
                    const currentCountryCode = @json($this->country_code);
                    if (currentCountryCode) {
                        const country = this.countries.find(c => c.code === currentCountryCode);
                        if (country) {
                            this.selectedCountry = country;
                        }
                    }
                },
                
                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.searchQuery = '';
                        this.filterCountries();
                    }
                },
                
                closeDropdown() {
                    this.isOpen = false;
                },
                
                filterCountries() {
                    if (!this.searchQuery) {
                        this.filteredCountries = this.countries;
                        return;
                    }
                    
                    const query = this.searchQuery.toLowerCase();
                    this.filteredCountries = this.countries.filter(country => 
                        country.label.toLowerCase().includes(query) ||
                        country.country.toLowerCase().includes(query) ||
                        country.code.includes(query)
                    );
                },
                
                selectCountry(country) {
                    this.selectedCountry = country;
                    this.isOpen = false;
                    this.searchQuery = '';
                    
                    // Update Livewire country_code
                    @this.set('country_code', country.code);
                },
            }
        }
        </script>
</div>