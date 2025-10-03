<a href="{{ route('cart') }}" class="relative flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-colors">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
    </svg>
    <span class="font-medium hidden sm:inline">Cart</span>
    @php $displayCount = ($count ?? 0) > 99 ? '99+' : ($count ?? 0); @endphp
    @if(($count ?? 0) > 0)
        <span class="absolute -top-2 -right-3 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold leading-none text-white bg-purple-600 rounded-full ring-2 ring-white shadow">{{ $displayCount }}</span>
    @endif
</a>


