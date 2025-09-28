<div class="w-full px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
        <a href="{{ route('admin.customers.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">New customer</a>
    </div>

    <div class="mb-4">
        <input type="text" wire:model.live="search" placeholder="Search name or email..." class="w-full border bg-white border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default Address</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($this->users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            @if($user->defaultAddress)
                                <div>{{ $user->defaultAddress->recipient_name }}</div>
                                <div class="text-gray-600">{{ $user->defaultAddress->line1 }}@if($user->defaultAddress->line2), {{ $user->defaultAddress->line2 }}@endif</div>
                                <div class="text-gray-600">{{ $user->defaultAddress->postal_code }} {{ $user->defaultAddress->city }}@if($user->defaultAddress->state), {{ $user->defaultAddress->state }}@endif, {{ $user->defaultAddress->country }}</div>
                                @if($user->defaultAddress->phone)<div class="text-gray-500">{{ $user->defaultAddress->phone }}</div>@endif
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="text-gray-500 hover:text-gray-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute text-left right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                    <div class="py-1">
                                        <a href="{{ route('admin.customers.manage', $user) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Manage</a>
                                        <a href="{{ route('admin.orders.index', ['user' => $user->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View orders</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($this->users->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $this->users->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>


