@php 
    $rootCategories = $categories->whereNull('parent_id');
    $allCategories = $categories->sortBy('name');
@endphp

<div class="relative" x-data="{ open: false }">
    <button 
        @click="open = !open" 
        @click.away="open = false"
        class="flex items-center justify-between w-full px-4 py-2 text-sm border border-gray-300 rounded-lg bg-white hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
    >
        <span class="text-gray-700">
            @if($categoryId)
                {{ $categories->firstWhere('id', $categoryId)?->name ?? 'Select Category' }}
            @else
                All Categories
            @endif
        </span>
        <svg 
            class="w-4 h-4 text-gray-400 transform transition-transform duration-200" 
            :class="{ 'rotate-180': open }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
        <!-- All Categories Option -->
        <button 
            type="button"
            @click="$dispatch('category-selected', null); open = false"
            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 transition-colors {{ !$categoryId ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700' }}"
        >
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                All Categories
            </div>
        </button>

        <div class="border-t border-gray-200"></div>

        <!-- Root Categories as Option Groups -->
        @foreach($rootCategories as $rootCategory)
            @php
                $subcategories = $categories->where('parent_id', $rootCategory->id);
            @endphp
            
            <!-- Group Header (Root Category) - Clickable -->
            <button 
                type="button"
                @click="$dispatch('category-selected', [{{ $rootCategory->id }}]); open = false"
                class="w-full px-4 py-2 bg-gray-50 border-b border-gray-200 hover:bg-gray-100 transition-colors {{ (int)$categoryId === (int)$rootCategory->id ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700' }}"
            >
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="text-sm font-semibold">{{ $rootCategory->name }}</span>
                </div>
            </button>

            <!-- Subcategories -->
            @if($subcategories->count() > 0)
                @foreach($subcategories as $subcategory)
                    <button 
                        type="button"
                        @click="$dispatch('category-selected', [{{ $subcategory->id }}]); open = false"
                        class="w-full px-8 py-2 text-left text-sm hover:bg-gray-100 transition-colors {{ (int)$categoryId === (int)$subcategory->id ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600' }}"
                    >
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 text-gray-400">└─</span>
                            {{ $subcategory->name }}
                        </div>
                    </button>
                @endforeach
            @endif

            <!-- Add spacing between groups -->
            @if(!$loop->last)
                <div class="border-b border-gray-100"></div>
            @endif
        @endforeach
    </div>
</div>