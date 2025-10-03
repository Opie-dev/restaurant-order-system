<div class="w-full mx-auto">
<section class="relative overflow-hidden" x-data="{ now: Date.now(), end: new Date('{{ $goLiveAtIso }}').getTime(), tick(){ this.now = Date.now() }, get remaining(){ return Math.max(0, this.end - this.now) }, get d(){ return Math.floor(this.remaining / (1000*60*60*24)) }, get h(){ return Math.floor((this.remaining % (1000*60*60*24)) / (1000*60*60)) }, get m(){ return Math.floor((this.remaining % (1000*60*60)) / (1000*60)) }, get s(){ return Math.floor((this.remaining % (1000*60)) / 1000) } }" x-init="setInterval(() => tick(), 1000)">
    <div class="relative isolate">
        <div class="mx-auto max-w-5xl px-4 pt-12 pb-10 text-center">
            <h1 class="text-3xl md:text-4xl font-bold tracking-tight">Get early access</h1>
            <p class="mt-3 text-gray-600 max-w-2xl mx-auto">Be the first to know when we go live and help shape the roadmap by voting on features.</p>
            <div class="mt-5 flex flex-col items-center gap-4">
                <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm text-blue-700">
                    <span class="font-semibold">Milestone</span>
                    <span>•</span>
                    <span>{{ $goLiveMilestone }}</span>
                </div>
                <div class="grid grid-cols-4 gap-3">
                    <div class="w-24 rounded-lg border bg-white px-3 py-2 shadow-sm">
                        <div class="text-2xl font-bold" x-text="d.toString().padStart(2,'0')"></div>
                        <div class="text-[11px] uppercase tracking-wide text-gray-500">Days</div>
                    </div>
                    <div class="w-24 rounded-lg border bg-white px-3 py-2 shadow-sm">
                        <div class="text-2xl font-bold" x-text="h.toString().padStart(2,'0')"></div>
                        <div class="text-[11px] uppercase tracking-wide text-gray-500">Hours</div>
                    </div>
                    <div class="w-24 rounded-lg border bg-white px-3 py-2 shadow-sm">
                        <div class="text-2xl font-bold" x-text="m.toString().padStart(2,'0')"></div>
                        <div class="text-[11px] uppercase tracking-wide text-gray-500">Minutes</div>
                    </div>
                    <div class="w-24 rounded-lg border bg-white px-3 py-2 shadow-sm">
                        <div class="text-2xl font-bold" x-text="s.toString().padStart(2,'0')"></div>
                        <div class="text-[11px] uppercase tracking-wide text-gray-500">Seconds</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="max-w-2xl mx-auto">
    <form wire:submit.prevent="submit" class="space-y-6 bg-white/70 backdrop-blur border rounded-xl p-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name (optional)</label>
                <input type="text" wire:model.defer="name" class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
</div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model.defer="email" required class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Milestone note (optional)</label>
            <input type="text" wire:model.defer="milestone" placeholder="e.g., Prefer January launch" class="mt-1 block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            @error('milestone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-700 mb-2">Vote for features</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($availableFeatures as $key => $label)
                    <label class="flex items-center gap-3 p-3 bg-white border rounded-lg cursor-pointer hover:border-blue-300">
                        <input type="checkbox" value="{{ $key }}" wire:model.defer="feature_votes" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('feature_votes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm">Subscribe</button>
            <span wire:loading class="text-sm text-gray-600">Submitting...</span>
        </div>
    </form>
</div>


