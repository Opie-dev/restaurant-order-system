<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Explore Stores</h1>
            <p class="text-gray-600">Choose a store to view its menu.</p>
        </div>

        @if($stores->isEmpty())
            <div class="bg-white rounded-lg border border-gray-200 p-8 text-center text-gray-500">No stores yet.</div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($stores as $store)
                    <a href="{{ route('menu.store.index', ['store' => $store->slug] ) }}" class="group block bg-white rounded-xl border border-gray-200 hover:border-purple-300 hover:shadow-lg transition-all overflow-hidden">
                        <!-- Store Cover Image -->
                        @if($store->cover_path)
                            <div class="relative h-32 md:h-42 lg:h-48 overflow-hidden">
                                <img src="{{ Storage::url($store->cover_path) }}" 
                                     alt="{{ $store->name }} cover" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute top-3 left-3">
                                    @if($store->isCurrentlyOpen())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Open
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500 text-white shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Closed
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- No cover image - show logo prominently or placeholder -->
                            <div class="relative h-32 md:h-42 lg:h-48 bg-gradient-to-br from-purple-100 to-purple-200 overflow-hidden">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    @if($store->logo_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($store->logo_path) }}" 
                                             alt="{{ $store->name }}" 
                                             class="h-16 w-16 object-contain rounded-lg">
                                    @else
                                        <div class="h-16 w-16 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute top-3 left-3">
                                    @if($store->isCurrentlyOpen())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500 text-white shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Open
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500 text-white shadow-sm">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Closed
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Store Info Section -->
                        <div class="p-5">
                            <!-- Store Name and Logo -->
                            <div class="flex items-center gap-3">
                                @if($store->logo_path && $store->cover_path)
                                    <!-- Show small logo when we have a cover -->
                                    <img src="{{ Storage::url($store->logo_path) }}" 
                                         alt="{{ $store->name }}" 
                                         class="h-8 w-8 object-contain rounded-lg flex-shrink-0">
                                @endif
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $store->name }}</h3>
                                    <p class="text-xs text-gray-500 truncate">/menu/{{ $store->slug }}</p>
                                </div>
                            </div>

                            <!-- Store Description -->
                            @if($store->description)
                                <p class="mt-3 text-sm text-gray-600 line-clamp-2">{{ $store->description }}</p>
                            @endif

                            <!-- Location -->
                            @if($store->city || $store->state)
                                <div class="mt-3 flex items-center text-xs text-gray-500">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ trim(($store->city ?? '').' '.($store->state ?? '')) }}
                                </div>
                            @endif

                            <!-- Next Opening Time -->
                            @if(!$store->isCurrentlyOpen() && $store->getNextOpeningTime())
                                <div class="mt-2 flex items-center text-xs text-gray-500">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $store->getNextOpeningTime() }}
                                </div>
                            @endif

                            <!-- Action Button -->
                            <div class="mt-4">
                                <span class="inline-flex items-center px-3 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium group-hover:bg-purple-700 transition-colors">
                                    View Menu
                                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>


