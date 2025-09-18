<div class="max-w-6xl mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
        <a href="{{ route('admin.customers.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">New customer</a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
        <input type="text" wire:model.live="search" placeholder="Search name or email..." class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($this->users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->role }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.index', ['user' => $user->id]) }}" class="text-purple-600 hover:text-purple-700">View orders</a>
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


