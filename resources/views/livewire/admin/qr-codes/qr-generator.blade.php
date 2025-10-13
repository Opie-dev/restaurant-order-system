<div>
    <div class="mb-4">
        <a href="{{ route('admin.tables.index') }}"
           class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Tables
        </a>
    </div>
    <div class="grid grid-cols-1 gap-6">
        <!-- Generate Single QR Code -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Generate Single QR Code</h3>
                
                <form wire:submit="generate">
                    <div class="space-y-4">
                        <!-- Table Selection -->
                        <div>
                            <label for="table_id" class="block text-sm font-medium text-gray-700 mb-2">Select Table *</label>
                            <select wire:model="table_id" 
                                    id="table_id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('table_id') border-red-500 @enderror">
                                <option value="">Choose a table</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}">{{ $table->display_name }} - {{ $table->store->name }}</option>
                                @endforeach
                            </select>
                            @error('table_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Expiration Date -->
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">Expiration Date (Optional)</label>
                            <input type="datetime-local" 
                                   wire:model="expires_at" 
                                   id="expires_at"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expires_at') border-red-500 @enderror">
                            @error('expires_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Leave empty for no expiration</p>
                        </div>

                        <!-- Generate Button -->
                        <div>
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Generate QR Code
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Instructions -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">How QR Codes Work</h3>
        <div class="text-sm text-blue-800 space-y-2">
            <p>• Each QR code links directly to the table's menu page</p>
            <p>• Customers can scan the QR code to access the menu and place orders</p>
            <p>• QR codes can be downloaded in multiple formats (PNG, PDF, with table number)</p>
            <p>• You can set expiration dates for temporary QR codes</p>
            <p>• Only one active QR code per table is allowed</p>
        </div>
    </div>
</div>