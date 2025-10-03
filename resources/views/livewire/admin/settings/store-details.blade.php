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
                <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and hyphens allowed. This will be your store's unique URL.</p>
                @error('slug')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea wire:model.blur="description" rows="3" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="Brief description of your restaurant"></textarea>
                @error('description')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>   

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" wire:model.blur="phone" pattern="[0-9+\-\s()]+" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="e.g. +60 12-345-6789" />
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
                       
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
