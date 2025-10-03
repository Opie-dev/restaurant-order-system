<?php

namespace App\Livewire;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.marketing')]
class Subscribe extends Component
{
    public string $name = '';
    public string $email = '';
    public ?string $milestone = null;
    /** @var array<int,string> */
    public array $feature_votes = [];

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', Rule::unique('subscriptions', 'email')],
            'milestone' => ['nullable', 'string', 'max:255'],
            'feature_votes' => ['array'],
            'feature_votes.*' => ['string', 'max:100'],
        ];
    }

    public function submit(): void
    {
        $validated = $this->validate();

        Subscription::create([
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'],
            'milestone' => $validated['milestone'] ?? null,
            'feature_votes' => array_values($validated['feature_votes'] ?? []),
        ]);

        $this->reset(['name', 'email', 'milestone', 'feature_votes']);

        $this->dispatch('flash', 'Thanks for subscribing!');
    }

    public function render()
    {
        $availableFeatures = [
            'multi-store' => 'Multi store',
            'faster-checkout' => 'One-click checkout',
            'order-tracking' => 'Real-time order tracking',
            'mobile-app' => 'Mobile app',
            'etc-etc' => 'Etc etc',
        ];

        $goLiveMilestone = 'Public beta on Nov 15, 2025';
        $goLiveAtIso = Carbon::create(2025, 11, 15, 0, 0, 0, config('app.timezone'))
            ->toIso8601String();

        return view('livewire.subscribe', [
            'availableFeatures' => $availableFeatures,
            'goLiveMilestone' => $goLiveMilestone,
            'goLiveAtIso' => $goLiveAtIso,
        ]);
    }
}
