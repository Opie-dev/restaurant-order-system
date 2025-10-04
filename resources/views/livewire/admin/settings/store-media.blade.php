<div class="w-full px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Store Media</h1>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Logo</h3>
                <div class="rounded-xl border border-gray-200 p-4 flex flex-col items-center">
                    <div class="relative h-28 w-28 flex items-center justify-center bg-white">
                        @if($logo_path)
                            <img src="{{ Storage::url($logo_path) }}" class="h-28 w-28 object-contain border border-gray-200 rounded-lg" alt="Current logo">
                            <button type="button" wire:click="deleteLogo" class="absolute top-1 right-1 bg-white rounded-full shadow p-1 hover:bg-red-50" title="Delete logo">
                                <svg class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        @else
                            <div class="text-xs text-gray-400">No logo</div>
                        @endif
                        <div class="absolute inset-0 bg-white/70 rounded-lg hidden items-center justify-center" wire:loading.flex wire:target="logo">
                            <svg class="w-5 h-5 animate-spin text-purple-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">(100×250px recommended)</p>
                    <input type="file" wire:model="logo" accept="image/*" class="hidden" id="logo-upload">
                    <label for="logo-upload" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-md text-sm font-medium hover:bg-purple-700 cursor-pointer">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v4h2V5h10v10H6v2h8a2 2 0 002-2V5a2 2 0 00-2-2H4z"/><path d="M9 7l-3 3h2v4h2v-4h2L9 7z"/></svg>
                        Change Logo
                    </label>
                </div>
                @if($logo && !$errors->has('logo'))
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $logo->temporaryUrl() }}" class="h-16 w-16 object-contain border border-gray-300 rounded-lg" alt="New logo preview">
                            <div class="flex-1 text-sm text-gray-700">New logo preview</div>
                            <button type="button" wire:click="$set('logo', null)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                        </div>
                    </div>
                @endif
                @error('logo')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Change Cover Image</h3>
                <div class="rounded-xl border border-gray-200 p-4 flex flex-col items-center">
                    <div class="relative h-28 w-56 bg-white flex items-center justify-center">
                        @if($cover_path)
                            <img src="{{ Storage::url($cover_path) }}" class="h-28 w-56 object-cover border border-gray-200 rounded-lg" alt="Current cover">
                            <button type="button" wire:click="deleteCover" class="absolute top-1 right-1 bg-white rounded-full shadow p-1 hover:bg-red-50" title="Delete cover">
                                <svg class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        @else
                            <div class="text-xs text-gray-400">No cover</div>
                        @endif
                        <div class="absolute inset-0 bg-white/70 rounded-lg hidden items-center justify-center" wire:loading.flex wire:target="cover">
                            <svg class="w-5 h-5 animate-spin text-purple-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">(1200×600px recommended)</p>
                    <input type="file" wire:model="cover" accept="image/*" class="hidden" id="cover-upload">
                    <label for="cover-upload" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-md text-sm font-medium hover:bg-purple-700 cursor-pointer">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v4h2V5h10v10H6v2h8a2 2 0 002-2V5a2 2 0 00-2-2H4z"/><path d="M9 7l-3 3h2v4h2v-4h2L9 7z"/></svg>
                        Change Cover Pic
                    </label>
                </div>
                @if($cover && !$errors->has('cover'))
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $cover->temporaryUrl() }}" class="h-24 w-48 object-cover border border-gray-300 rounded-lg" alt="New cover preview">
                            <div class="flex-1 text-sm text-gray-700">New cover preview</div>
                            <button type="button" wire:click="$set('cover', null)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                        </div>
                    </div>
                @endif
                @error('cover')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            
        </div>

        <div class="pt-6 flex items-center justify-end" x-data="{saved:false}" x-on:media-saved.window="saved=true; setTimeout(()=> saved=false, 1200)">
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
        </div>
    </div>
</div>


