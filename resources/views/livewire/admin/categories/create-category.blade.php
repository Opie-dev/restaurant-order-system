<div class="w-full">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Create Category</h1>
                <p class="mt-1 text-sm text-gray-600">Add a new category to organize your menu items.</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Categories
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white shadow rounded-lg">
        <form wire:submit.prevent="create" class="p-6 space-y-6">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name"
                    wire:model.blur="name" 
                    class="w-full rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 border @error('name') border-red-300 @else border-gray-300 @enderror" 
                    placeholder="e.g. Burgers, Appetizers, Desserts"
                    autofocus
                />
                @error('name') 
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div> 
                @enderror
            </div>

            <!-- Parent Category Field -->
            <div>
                <label for="parentLevel1Id" class="block text-sm font-medium text-gray-700 mb-2">
                    Parent Category
                </label>
                <select 
                    id="parentLevel1Id"
                    wire:model="parentLevel1Id" 
                    class="w-full rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 border @error('parentLevel1Id') border-red-300 @else border-gray-300 @enderror"
                >
                    <option value="">Root (no parent)</option>
                    @foreach($this->rootCategories as $root)
                        <option value="{{ $root->id }}">{{ $root->name }}</option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">Select a parent category to create a subcategory.</p>
                @error('parentLevel1Id') 
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div> 
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Create Category
                </button>
            </div>
        </form>
    </div>

    <!-- Help Text -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Category Tips</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Use clear, descriptive names that customers will understand</li>
                        <li>Create subcategories by selecting a parent category</li>
                        <li>Categories can be activated or deactivated after creation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
