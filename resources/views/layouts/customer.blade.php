<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Restaurant') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 flex flex-col" x-data="{ flash: null, flashType: null }" x-init="window.addEventListener('flash', e => { let d = e.detail; // Livewire may wrap payloads as [payload] when not using named args
    if (d && typeof d === 'object' && d !== null && !('message' in d) && 0 in d) { d = d[0]; }
    if (typeof d === 'object' && d !== null) { flash = d.message ?? ''; flashType = d.type ?? null; } else { flash = d ?? ''; flashType = null; } })">
    <!-- Header -->
   

    <!-- Main Content -->
    <main class="flex-1 overflow-hidden">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <template x-if="flash">
        <div class="fixed bottom-4 right-4 text-white px-4 py-3 rounded-lg z-50 shadow-lg min-w-64"
             :class="flashType === 'error' ? 'bg-red-600' : 'bg-black'"
             x-data="{ 
                 progress: 100,
                 duration: 3000,
                 startTime: Date.now()
             }"
             x-init="
                 const timer = setInterval(() => {
                     const elapsed = Date.now() - startTime;
                     const remaining = Math.max(0, duration - elapsed);
                     progress = (remaining / duration) * 100;
                     if (remaining <= 0) {
                         clearInterval(timer);
                         flash = null;
                     }
                 }, 50);
                 setTimeout(() => flash = null, duration);
             "
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2">
            
            <!-- Toast Content -->
            <div class="flex items-center justify-between mb-2">
                <span x-text="flash" class="text-sm font-medium"></span>
                <button @click="flash = null" class="text-gray-300 hover:text-white ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full rounded-full h-1" :class="flashType === 'error' ? 'bg-red-700' : 'bg-gray-700'">
                <div class="bg-white h-1 rounded-full transition-all duration-50 ease-linear" 
                     :style="`width: ${progress}%`"></div>
            </div>
        </div>
    </template>
    @livewireScripts
</body>
</html>
