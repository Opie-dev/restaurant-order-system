<div x-data="{ showCreate:false }">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Categories</h1>
        <button type="button" @click="showCreate=true" class="px-4 py-2 bg-indigo-600 text-white rounded">Add category</button>
    </div>

    <!-- Create Modal -->
    <div x-cloak x-show="showCreate" class="fixed inset-0 z-20 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showCreate=false"></div>
        <div class="relative bg-white rounded-lg shadow max-w-lg w-full p-6">
            <h2 class="text-lg font-semibold mb-4">Create category</h2>
            <form wire:submit.prevent="create" @submit="showCreate=false" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" wire:model.blur="name" class="w-full border rounded px-3 py-2" placeholder="e.g. Sushi" />
                    @error('name') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Parent (optional)</label>
                    <select wire:model="parentLevel1Id" class="w-full border rounded px-3 py-2">
                        <option value="">Root (no parent)</option>
                        @isset($rootCategories)
                            @foreach($rootCategories as $root)
                                <option value="{{ $root->id }}">{{ $root->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                    @error('parentLevel1Id') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showCreate=false" class="px-4 py-2 border rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white border rounded">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Parent</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->categories as $row)
                    <tr class="border-t">
                       
                        <td class="px-4 py-2">{{ $row->name }}</td>
                        <td class="px-4 py-2">{{ optional($this->categories->firstWhere('id', $row->parent_id))->name ?? '—' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-sm {{ $row->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $row->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" class="text-gray-700 hover:text-gray-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                    <button 
                                        wire:click="toggle({{ $row->id }})" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    >
                                        {{ $row->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button 
                                        wire:click="deleteCategory({{ $row->id }})"
                                        onclick="return confirm('Delete this category?')"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
