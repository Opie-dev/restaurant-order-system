<?php

namespace App\Livewire\Shared;

use App\Services\StoreService;
use Livewire\Component;
use App\Models\Store;

class HeaderBrand extends Component
{
    public ?Store $store = null;

    public function mount(StoreService $storeService): void
    {
        $this->store = $storeService->getCurrentStore();
    }

    public function render()
    {
        return view('livewire.shared.header-brand');
    }
}
