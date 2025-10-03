<div class="w-full px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Opening Hours</h1>
    <form wire:submit.prevent="save" class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div class="text-sm">
                @if($always_open)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <p class="text-yellow-800 font-semibold">🎉 Always Open Enabled</p>
                                <p class="text-yellow-700 mt-1">Your store will be available 24/7. Daily opening hours will be disabled.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-600">Configure specific opening hours for each day of the week.</p>
                @endif
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-700">Always Open</div>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.live="always_open" class="sr-only">
                    <div class="w-11 h-6 rounded-full relative transition-colors {{ $always_open ? 'bg-purple-600' : 'bg-gray-200' }}">
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform {{ $always_open ? 'translate-x-0' : 'translate-x-5' }}"></div>
                    </div>
                </label>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opening Time</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Closing Time</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hours as $index => $row)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">{{ $row['day'] }}</td>
                            <td class="px-4 py-2">
                                @if(!$row['enabled'])
                                    <span class="text-sm text-gray-400">closed</span>
                                @else
                                    <input type="time" wire:model.live="hours.{{ $index }}.open" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2 focus:border-purple-500 focus:ring-purple-500 {{ $always_open ? 'cursor-not-allowed disabled:bg-gray-100' : '' }}" {{ $always_open ? 'disabled' : '' }}>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if(!$row['enabled'])
                                    <span class="text-sm text-gray-400">closed</span>
                                @else
                                    <input type="time" wire:model.live="hours.{{ $index }}.close" class="w-full rounded-lg border-2 border-gray-300 px-3 py-2 focus:border-purple-500 focus:ring-purple-500 {{ $always_open ? 'cursor-not-allowed disabled:bg-gray-100' : '' }}" {{ $always_open ? 'disabled' : '' }}>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <label class="inline-flex items-center {{ $always_open ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                    <input type="checkbox" wire:model.live="hours.{{ $index }}.enabled" class="sr-only" {{ $always_open ? 'disabled' : '' }}>
                                    <div class="w-11 h-6 rounded-full relative transition-colors {{ $row['enabled'] ? 'bg-purple-600' : 'bg-gray-200' }} {{ $always_open ? 'opacity-50' : '' }}">
                                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform {{ $row['enabled'] ? 'translate-x-0' : 'translate-x-5' }}"></div>
                                    </div>
                                </label>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
            <div wire:loading.flex wire:target="save" class="items-center text-sm text-purple-700 bg-purple-50 px-3 py-1 rounded gap-2">
                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                Saving...
            </div>
            <button type="submit" wire:loading.attr="disabled" wire:target="save" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-60 disabled:cursor-not-allowed">Save Hours</button>
        </div>
    </form>
</div>


