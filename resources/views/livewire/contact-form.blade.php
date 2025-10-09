<div>
    @if($sent)
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800">
            Mesej berjaya dihantar. Terima kasih!
        </div>
    @endif

    @error('rate')
        <div class="mb-4 p-3 rounded-md bg-yellow-50 border border-yellow-200 text-yellow-800">{{ $message }}</div>
    @enderror

    <form wire:submit.prevent="submit" class="grid grid-cols-1 gap-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                <input wire:model.defer="name" id="name" type="text" class="p-2 border mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nama penuh" />
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mel</label>
                <input wire:model.defer="email" id="email" type="email" class="p-2 border  mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="nama@contoh.com" />
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="subject" class="block text-sm font-medium text-gray-700">Subjek</label>
            <input wire:model.defer="subject" id="subject" type="text" class="p-2 border mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tajuk mesej" />
            @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Mesej</label>
            <textarea wire:model.defer="message" id="message" rows="5" class="p-2 border mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tulis mesej anda di sini..."></textarea>
            @error('message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Honeypot field (hidden) -->
        <div class="hidden">
            <label for="hp">Jangan isi</label>
            <input type="text" id="hp" wire:model.defer="hp" autocomplete="off" />
        </div>

        <div class="pt-2">
            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition disabled:opacity-60" wire:loading.attr="disabled">
                <svg wire:loading class="-ml-1 mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span wire:loading.remove>Hantar Mesej</span>
                <span wire:loading>Memproses...</span>
            </button>
        </div>
    </form>
</div>


