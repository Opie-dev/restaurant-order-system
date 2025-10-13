<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-semibold text-gray-900">Tables & QR Codes Management</h1>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:space-x-3 sm:gap-0">
            <a href="{{ route('admin.tables.create') }}" 
                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors w-full sm:w-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Table
            </a>
            <form method="POST" action="{{ route('admin.qr-codes.generate-all') }}" class="inline w-full sm:w-auto">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors w-full sm:w-auto"
                        onclick="return confirm('This will generate QR codes for all active tables. Continue?')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Generate All QR Codes
                </button>
            </form>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" 
                       wire:model.live="search" 
                       placeholder="Search tables..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model.live="statusFilter" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                <select wire:model.live="perPage" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tables List -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="p-6">
            <div class="overflow-x-auto">
                @if($tables->isNotEmpty())
                    <!-- Desktop table -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Codes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tables as $table)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $table->display_name }}</div>
                                                @if($table->location_description)
                                                    <div class="text-sm text-gray-500">{{ $table->location_description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $table->capacity }} people
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="toggleStatus({{ $table->id }})"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $table->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }} transition-colors">
                                            {{ $table->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            @php
                                                $activeQrCode = $table->qrCodes->where('is_active', true)->first();
                                            @endphp
                                            
                                            @if($activeQrCode)
                                                <div class="flex items-center justify-between bg-gray-50 rounded px-2 py-1">
                                                    <div class="flex items-center space-x-2">
                                                        <img src="{{ $activeQrCode->getQrCodeImageUrl() }}" alt="QR Table {{ $table->table_number }}" class="h-10 w-10 rounded border border-gray-200 bg-white" />
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Active
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500">No QR code</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $table->orders->count() }} orders
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            {{-- <a href="{{ route('admin.tables.show', $table) }}" 
                                            class="text-blue-600 hover:text-blue-900">View</a> --}}
                                            <a href="{{ route('admin.tables.edit', $table) }}" 
                                            class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 border border-green-200 rounded px-2 py-1 transition-colors"
                                            title="Edit">
                                                <svg class="w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13h3l9-9a2.003 2.003 0 00-2.828-2.828l-9 9v3zm-6 7h6a2 2 0 002-2v-6l-8 8z"/>
                                                </svg>
                                            </a>
                                            <button
                                                type="button"
                                                wire:click="deleteTable({{ $table->id }})"
                                                wire:confirm="Are you sure you want to delete this table?"
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 border border-red-200 rounded px-2 py-1 transition-colors"
                                                title="Delete"
                                            >
                                                <svg class="h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v2H7V5a2 2 0 012-2zm-6 6h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center">
                        <!-- Icon -->
                        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        
                        <!-- Main Message -->
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No tables found</h3>
                        
                        <!-- Sub Message -->
                        <p class="text-sm text-gray-500 mb-6">Get started by creating your first table.</p>
                        
                        <!-- Call to Action Button -->
                        <a href="{{ route('admin.tables.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Table
                        </a>
                    </div>
                @endif
            </div>

            @if($tables->hasPages())
                <div class="mt-6">
                    {{ $tables->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- QR Code Generation Modal -->
    @if($showQrForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showQrForm') }" x-show="show">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="generateQrCode">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4m-4-8h4M4 4h4m12 0h4m-4 8h4M4 20h4m12 0h4"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Generate QR Code</h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Table</label>
                                            <select wire:model="selectedTableId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select a table</option>
                                                @foreach($tablesForSelect as $t)
                                                    <option value="{{ $t->id }}">Table {{ $t->table_number }}</option>
                                                @endforeach
                                            </select>
                                            @error('selectedTableId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Expires At (Optional)</label>
                                            <input type="datetime-local" wire:model="expiresAt" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                            @error('expiresAt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Generate QR Code
                            </button>
                            <button type="button" wire:click="hideQrForm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>