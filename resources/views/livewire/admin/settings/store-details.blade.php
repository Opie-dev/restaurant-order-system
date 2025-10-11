<div class="w-full px-6">

    <h1 class="text-2xl font-bold text-gray-900 mb-4">Store Details</h1>
    <form wire:submit.prevent="saveDetails" class="space-y-6 mb-4">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Store Name</label>
                <input type="text" wire:model.blur="name" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Store URL Slug</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        {{ config('app.url') }}/menu/
                    </span>
                    <input type="text" wire:model.blur="slug" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="store-slug" />
                </div>
                <div class="text-right mt-2">
                    <button
                        type="button"
                        x-data="{
                            copied: false,
                            copyUrl() {
                                const url = '{{ config('app.url') }}/menu/{{ $slug ?? $store->slug ?? '' }}';
                                navigator.clipboard.writeText(url).then(() => {
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 1500);
                                    $dispatch('notify', { type: 'success', message: 'URL copied to clipboard!' });
                                }).catch(() => {
                                    $dispatch('notify', { type: 'error', message: 'Failed to copy URL' });
                                });
                            }
                        }"
                        @click="copyUrl()"
                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 mr-2"
                        title="Copy store URL"
                    >
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8a2 2 0 002-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2h2"></path>
                        </svg>
                        <span x-show="!copied">Copy</span>
                        <span x-show="copied" x-cloak>Copied!</span>
                    </button>
                    <a
                        href="{{ config('app.url') }}/menu/{{ $slug ?? $store->slug ?? '' }}"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-300"
                        title="Open store page"
                    >
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10v11a1 1 0 001 1h11a1 1 0 001-1V10"></path>
                        </svg>
                        Open
                    </a>
                </div>
                <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and hyphens allowed. This will be your store's unique URL.</p>
                @error('slug')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea wire:model.blur="description" rows="3" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="Brief description of your restaurant"></textarea>
                @error('description')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>   

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                               wire:model.blur="phone" 
                               x-model="phoneNumber"
                               @input="updatePhoneNumber()"
                               class="flex-1 rounded-r-lg border-2 border-gray-300 border-l-0 w-full px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" 
                               placeholder="Enter phone number" />
                    </div>
                    @error('phone')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" wire:model.blur="email" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                    @error('email')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        These details will be used across your restaurant system
                    </div>
                    <div class="flex space-x-3">
                       
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2" wire:loading.attr="disabled" wire:target="saveTax">
                            <svg wire:loading wire:target="saveDetails" class="animate-spin -ml-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4z"/>
                            </svg>
                            <span>Save</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form wire:submit.prevent="saveTax" class="space-y-6 mb-4">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Tax Settings</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                <div class="flex">
                    <input type="number" wire:model.blur="tax_rate" step="0.01" min="0" max="100" class="flex-1 rounded-l-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="0.00" />
                    <span class="inline-flex items-center px-3 rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">%</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Enter the tax rate as a percentage (e.g., 6.5 for 6.5%)</p>
                @error('tax_rate')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Tax rate will be applied to all orders
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2" wire:loading.attr="disabled" wire:target="saveTax">
                            <svg wire:loading wire:target="saveTax" class="animate-spin -ml-1 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4z"/>
                            </svg>
                            <span>Save</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form wire:submit.prevent="saveSocialMedia" class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Social media page links and other links</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Google Map Link</label>
                    <input type="url" wire:model.blur="social_google_map" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="https://maps.app.goo.gl/..." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">facebook.com/</span>
                        <input type="text" wire:model.blur="social_facebook" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="YourPage" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">instagram.com/</span>
                        <input type="text" wire:model.blur="social_instagram" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="YourHandle" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiktok</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">tiktok.com/</span>
                        <input type="text" wire:model.blur="social_tiktok" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="@YourHandle" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Youtube</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">youtube.com/</span>
                        <input type="text" wire:model.blur="social_youtube" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="@YourChannel" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Other</label>
                    <input type="text" wire:model.blur="social_other" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="yourdomain.com" />
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end" x-data="{saved:false}" x-on:social-saved.window="saved=true; setTimeout(()=> saved=false, 1200)">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2" wire:loading.attr="disabled" wire:target="saveTax">
                    <svg wire:loading wire:target="saveSocialMedia" class="animate-spin -ml-1 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4z"/>
                    </svg>
                    <span>Save</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function phoneInput() {
    return {
        isOpen: false,
        searchQuery: '',
        phoneNumber: '',
        selectedCountry: {
            code: '+60',
            label: 'MY (+60)',
            country: 'Malaysia'
        },
        countries: @json($this->countryCodes),
        filteredCountries: @json($this->countryCodes),
        
        init() {
            // Set default country based on current store country_code
            const currentCountryCode = @json($this->country_code);
            if (currentCountryCode) {
                const country = this.countries.find(c => c.code === currentCountryCode);
                if (country) {
                    this.selectedCountry = country;
                }
            }
            
            // Parse existing phone number
            const currentPhone = @json($this->phone) || '';
            if (currentPhone) {
                this.parseExistingPhone(currentPhone);
            }
        },
        
        parseExistingPhone(phone) {
            // Find country code in phone number
            const countries = this.countries.sort((a, b) => b.code.length - a.code.length);
            
            for (const country of countries) {
                if (phone.startsWith(country.code)) {
                    this.selectedCountry = country;
                    this.phoneNumber = phone.substring(country.code.length);
                    return;
                }
            }
            
            // If no country code found, use the phone as is
            this.phoneNumber = phone;
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
            this.updatePhoneNumber();
            
            // Update Livewire country_code
            @this.set('country_code', country.code);
        },
        
        updatePhoneNumber() {
            // Update Livewire phone with just the phone number (continuation of country code)
            @this.set('phone', this.phoneNumber);
        },
    }
}
</script>
