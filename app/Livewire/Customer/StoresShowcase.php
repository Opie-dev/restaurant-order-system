<?php

namespace App\Livewire\Customer;

use App\Models\Store;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.customer')]
class StoresShowcase extends Component
{
    public function render()
    {
        $stores = Store::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'logo_path', 'cover_path', 'city', 'state', 'settings']);

        return view('livewire.customer.stores-showcase', [
            'stores' => $stores,
        ]);
    }
}
