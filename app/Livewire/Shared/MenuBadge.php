<?php

namespace App\Livewire\Shared;

use App\Models\Store;
use Livewire\Component;
use App\Services\StoreService;

class MenuBadge extends Component
{
    public ?Store $store = null;

    public function mount(StoreService $storeService): void
    {
        $this->store = $storeService->getCurrentStore();
    }

    public function render()
    {
        return view('livewire.shared.menu-badge');
    }
}
