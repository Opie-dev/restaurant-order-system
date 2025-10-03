<div class="w-full px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Store Media</h1>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-8">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Store Logo</label>
            <div class="space-y-4">
                @if($logo_path)
                    <div class="flex items-center space-x-4">
                        <img src="{{ Storage::url($logo_path) }}" class="h-16 w-16 object-contain border border-gray-300 rounded-lg" alt="Current logo">
                        <div class="text-sm text-gray-600">Current logo</div>
                    </div>
                @endif
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                    <input type="file" wire:model="logo" accept="image/*" class="hidden" id="logo-upload">
                    <label for="logo-upload" class="cursor-pointer text-sm font-medium text-purple-600 hover:text-purple-500">Upload a logo</label>
                </div>
                @if($logo)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $logo->temporaryUrl() }}" class="h-16 w-16 object-contain border border-gray-300 rounded-lg" alt="New logo preview">
                            <div class="flex-1 text-sm text-gray-700">New logo preview</div>
                            <button type="button" wire:click="$set('logo', null)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                        </div>
                    </div>
                @endif
            </div>
            @error('logo')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
            <div class="space-y-4">
                @if($cover_path)
                    <div class="flex items-center space-x-4">
                        <img src="{{ Storage::url($cover_path) }}" class="h-24 w-48 object-cover border border-gray-300 rounded-lg" alt="Current cover">
                        <div class="text-sm text-gray-600">Current cover image</div>
                    </div>
                @endif
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                    <input type="file" wire:model="cover" accept="image/*" class="hidden" id="cover-upload">
                    <label for="cover-upload" class="cursor-pointer text-sm font-medium text-purple-600 hover:text-purple-500">Upload a cover image</label>
                </div>
                @if($cover)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $cover->temporaryUrl() }}" class="h-24 w-48 object-cover border border-gray-300 rounded-lg" alt="New cover preview">
                            <div class="flex-1 text-sm text-gray-700">New cover preview</div>
                            <button type="button" wire:click="$set('cover', null)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                        </div>
                    </div>
                @endif
            </div>
            @error('cover')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3" x-data="{saved:false}" x-on:media-saved.window="saved=true; setTimeout(()=> saved=false, 1200)">
            <div x-show="saved" x-cloak class="text-sm text-green-700 bg-green-100 px-3 py-1 rounded inline-flex items-center gap-2"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1">
                <svg class="w-4 h-4 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                Saved
            </div>
            <div wire:loading.flex wire:target="saveMedia" class="items-center text-sm text-purple-700 bg-purple-50 px-3 py-1 rounded gap-2 hidden">
                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                Saving...
            </div>
            <button type="button" wire:click="saveMedia" wire:loading.attr="disabled" wire:target="saveMedia" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-60 disabled:cursor-not-allowed">Save Media</button>
        </div>
    </div>
</div>


