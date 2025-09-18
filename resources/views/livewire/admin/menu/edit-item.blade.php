<div class="max-w-2xl mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-semibold">{{ $isEdit ? 'Edit' : 'Create' }} Menu Item</h1>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" wire:model.blur="name" class="w-full border rounded px-3 py-2" />
            @error('name') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea wire:model.blur="description" rows="4" class="w-full border rounded px-3 py-2"></textarea>
            @error('description') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Price</label>
                <input type="number" step="0.01" min="0" wire:model.blur="price" class="w-full border rounded px-3 py-2" />
                @error('price') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Stock</label>
                <input type="number" step="1" min="0" wire:model.blur="stock" class="w-full border rounded px-3 py-2" />
                @error('stock') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Category</label>
                <select wire:model.blur="category_id" class="w-full border rounded px-3 py-2">
                    <option value="">Select category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tag</label>
                <select wire:model.blur="tag" class="w-full border rounded px-3 py-2">
                    <option value="">None</option>
                    <option value="popular">Popular</option>
                    <option value="bestseller">Bestseller</option>
                </select>
                @error('tag') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input id="is_active" type="checkbox" wire:model.live="is_active" class="rounded" />
            <label for="is_active">Active</label>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-medium">Image</label>
            <input type="file" wire:model="image" accept="image/*" />
            @error('image') <div class="text-sm text-red-600">{{ $message }}</div> @enderror

            <div class="grid grid-cols-2 gap-4">
                @if($image)
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Preview</div>
                        <img src="{{ $image->temporaryUrl() }}" class="rounded border" />
                    </div>
                @endif
                @if($isEdit && $menuItem && $menuItem->image_path)
                    <div>
                        <div class="text-sm text-gray-600 mb-1">Current</div>
                        <img src="{{ asset('storage/' . $menuItem->image_path) }}" class="rounded border" />
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.menu.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
        </div>
    </form>
</div>
