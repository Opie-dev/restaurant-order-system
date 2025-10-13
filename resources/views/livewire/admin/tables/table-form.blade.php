<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $table ? 'Edit Table' : 'Create New Table' }}</h1>
        <a href="{{ route('admin.tables.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>
    
    <!-- QR Code Actions -->
    @if($table)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">QR Code</label>
            <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                    @if($latestQrCode)
                        <img src="{{ $latestQrCode->getQrCodeImageUrl() }}" alt="QR Table {{ $table->table_number }}" class="h-20 w-20 rounded border border-gray-200 bg-white" />
                        <div>
                            @if($latestQrCode->is_active)
                                <div class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Active</div>
                            @else
                                <div class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Disabled</div>
                            @endif
                            <div class="text-xs text-gray-500 mt-1">Generated: {{ $latestQrCode->generated_at?->format('Y-m-d H:i') }}</div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500">No QR code yet</div>
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button"
                            wire:click="generateQrCode"
                            wire:loading.attr="disabled"
                            wire:target="generateQrCode"
                            class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-xs font-medium rounded hover:bg-purple-700 disabled:opacity-60 disabled:cursor-not-allowed"
                            title="Generate">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" wire:loading.class="animate-spin" wire:target="generateQrCode">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="sr-only">Generate</span>
                    </button>
                    @if($latestQrCode)
                        <button type="button"
                                wire:click="emailQrCode"
                                class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700"
                                title="Email QR Code">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            <span class="sr-only">Email</span>
                        </button>
                        <a href="{{ $latestQrCode->getQrCodeImageUrl() }}"
                           target="_blank"
                           rel="noopener"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded hover:bg-gray-200 border border-gray-300"
                           title="View QR Code">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                            </svg>
                            <span class="sr-only">View</span>
                        </a>
                        @if($latestQrCode->is_active)
                            <button type="button"
                                    wire:click="disableQrCode"
                                    class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700"
                                    wire:confirm="Disable the current QR code?"
                                    title="Disable">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6" />
                                </svg>
                                <span class="sr-only">Disable</span>
                            </button>
                        @else
                            <button type="button"
                                    wire:click="enableQrCode"
                                    class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700"
                                    title="Enable">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v14l11-7-11-7z" />
                                </svg>
                                <span class="sr-only">Enable</span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="save">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Table Number -->
                    <div>
                        <label for="table_number" class="block text-sm font-medium text-gray-700 mb-2">Table Number *</label>
                        <input type="text" 
                               wire:model="table_number" 
                               id="table_number"
                               placeholder="e.g., 1, A1, VIP-1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('table_number') border-red-500 @enderror">
                        @error('table_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">Capacity *</label>
                        <input type="number" 
                               wire:model="capacity" 
                               id="capacity"
                               min="1" 
                               max="20"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('capacity') border-red-500 @enderror">
                        @error('capacity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Location Description -->
                    <div class="md:col-span-2">
                        <label for="location_description" class="block text-sm font-medium text-gray-700 mb-2">Location Description (Optional)</label>
                        <input type="text" 
                               wire:model="location_description" 
                               id="location_description"
                               placeholder="e.g., Near window, Patio area, Private booth"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location_description') border-red-500 @enderror">
                        @error('location_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   wire:model="is_active" 
                                   id="is_active"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                        </div>
                        @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.tables.index') }}" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        {{ $table ? 'Update' : 'Create' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>