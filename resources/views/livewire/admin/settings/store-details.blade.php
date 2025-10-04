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
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form wire:submit.prevent="saveSocialMedia" class="space-y-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Social media page links and other links</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Google Map Link</label>
                    <input type="url" wire:model.blur="social_google_map" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="https://maps.app.goo.gl/..." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">facebook.com/</span>
                        <input type="text" wire:model.blur="social_facebook" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="YourPage" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">instagram.com/</span>
                        <input type="text" wire:model.blur="social_instagram" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="YourHandle" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tiktok</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">tiktok.com/</span>
                        <input type="text" wire:model.blur="social_tiktok" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="@YourHandle" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Youtube</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">youtube.com/</span>
                        <input type="text" wire:model.blur="social_youtube" class="flex-1 rounded-r-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="@YourChannel" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Other</label>
                    <input type="text" wire:model.blur="social_other" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2.5 focus:border-purple-500 focus:ring-purple-500" placeholder="yourdomain.com" />
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end" x-data="{saved:false}" x-on:social-saved.window="saved=true; setTimeout(()=> saved=false, 1200)">
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
                <button type="submit" class="ml-auto px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Save</button>
            </div>
        </div>
    </form>
</div>
