<div x-data="{ 
    showCreate: false,
    expandedCategories: new Set(),
    toggleCategory(id) {
        if (this.expandedCategories.has(id)) {
            this.expandedCategories.delete(id);
        } else {
            this.expandedCategories.add(id);
        }
    },
    isExpanded(id) {
        return this.expandedCategories.has(id);
    }
}">
    <!-- Header with Search and Add Button -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Categories</h1>
        <div class="flex items-center gap-3">
            <!-- Search Input -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search categories..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-64"
                />
            </div>
            <!-- Add Button -->
            <button 
                type="button" 
                @click="showCreate=true" 
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Category
            </button>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-cloak x-show="showCreate" class="fixed inset-0 z-20 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showCreate=false"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6 mx-4">
            <h2 class="text-lg font-semibold mb-4">Create Category</h2>
            <form wire:submit.prevent="create" @submit="showCreate=false" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input 
                        type="text" 
                        wire:model.blur="name" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                        placeholder="e.g. Burgers" 
                    />
                    @error('name') 
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div> 
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category (optional)</label>
                    <select 
                        wire:model="parentLevel1Id" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Root (no parent)</option>
                        @isset($rootCategories)
                            @foreach($rootCategories as $root)
                                <option value="{{ $root->id }}">{{ $root->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                    @error('parentLevel1Id') 
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div> 
                    @enderror
                </div>
                <div class="flex items-center justify-end gap-3 pt-4">
                    <button 
                        type="button" 
                        @click="showCreate=false" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-12 gap-4 px-6 py-3 text-sm font-medium text-gray-700">
                <div class="col-span-7">Name</div>
                <div class="col-span-3">Status</div>
                <div class="col-span-2 text-right">Actions</div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-gray-200">
            @forelse($hierarchicalCategories as $category)
                <div 
                    x-show="{{ $category->parent_id === null ? 'true' : 'expandedCategories.has(' . $category->parent_id . ')' }}"
                    @if($category->hasChildren)
                        @click="toggleCategory({{ $category->id }})"
                        class="px-6 py-4 hover:bg-indigo-50 hover:border-indigo-200 transition-colors cursor-pointer"
                    @else
                        class="px-6 py-4 hover:bg-gray-50 transition-colors"
                    @endif
                >
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <!-- Name Column with Hierarchy -->
                        <div class="col-span-7">
                            <div class="flex items-center" style="padding-left: {{ $category->level * 24 }}px;">
                                @if($category->level > 0)
                                    <span class="text-gray-400 mr-2">
                                        @if($category->level === 1)
                                            └─
                                        @elseif($category->level === 2)
                                            &nbsp;&nbsp;&nbsp;└─
                                        @else
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─
                                        @endif
                                    </span>
                                @endif
                                
                                @if($category->hasChildren)
                                    <div class="mr-2 text-gray-500 flex items-center">
                                        <svg 
                                            class="h-4 w-4 transform transition-transform duration-200" 
                                            :class="{ 'rotate-90': isExpanded({{ $category->id }}) }"
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                @else
                                    <span class="w-6 mr-2"></span>
                                @endif
                                
                                <span class="font-medium text-gray-900">{{ $category->name }}</span>
                            </div>
                        </div>

                        <!-- Status Column -->
                        <div class="col-span-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                @if($category->is_active)
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    Active
                                @else
                                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                    Inactive
                                @endif
                            </span>
                        </div>

                        <!-- Actions Column -->
                        <div class="col-span-2 text-right">
                            <div class="flex items-center justify-end gap-2" @click.stop>
                                <!-- Edit Button -->
                                <button 
                                    wire:click="toggle({{ $category->id }})"
                                    class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                                    title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($category->is_active)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @endif
                                    </svg>
                                </button>

                                <!-- Delete Button -->
                                <button 
                                    wire:click="deleteCategory({{ $category->id }})"
                                    onclick="return confirm('Are you sure you want to delete this category? Please be aware that this action will remove all menu items under this category.')"
                                    class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                    title="Delete"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No categories found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($search)
                            No categories match your search criteria.
                        @else
                            Get started by creating your first category.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>