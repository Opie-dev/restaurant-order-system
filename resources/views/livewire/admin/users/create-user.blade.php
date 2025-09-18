<div class="max-w-4xl mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create Customer</h1>

    <form wire:submit.prevent="save" class="space-y-6 bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" wire:model.blur="name" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model.blur="email" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('email')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" wire:model.blur="password" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('password')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Label (optional)</label>
                <input type="text" wire:model.blur="label" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('label')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Recipient name</label>
                <input type="text" wire:model.blur="recipient_name" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('recipient_name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" wire:model.blur="phone" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
            @error('phone')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Address line 1</label>
            <input type="text" wire:model.blur="line1" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
            @error('line1')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Address line 2 (optional)</label>
            <input type="text" wire:model.blur="line2" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
            @error('line2')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">City</label>
                <input type="text" wire:model.blur="city" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('city')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">State</label>
                <input type="text" wire:model.blur="state" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('state')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Postal code</label>
                <input type="text" wire:model.blur="postal_code" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
                @error('postal_code')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Country</label>
            <input type="text" wire:model.blur="country" class="mt-1 w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" />
            @error('country')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Create</button>
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 border rounded-lg ml-2">Cancel</a>
        </div>
    </form>
</div>


