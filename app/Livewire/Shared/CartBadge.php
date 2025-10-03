<?php

namespace App\Livewire\Shared;

use App\Services\CartService;
use Livewire\Component;

class CartBadge extends Component
{
    public int $count = 0;

    protected $listeners = [
        'cart.updated' => 'refreshCount',
        'cart.item-added' => 'refreshCount',
        'cart.item-removed' => 'refreshCount',
        'cart.cleared' => 'refreshCount',
    ];

    public function mount(CartService $cartService): void
    {
        $this->count = $this->fetchCount($cartService);
    }

    public function refreshCount(CartService $cartService): void
    {
        $this->count = $this->fetchCount($cartService);
    }

    private function fetchCount(CartService $cartService): int
    {
        try {
            return (int) $cartService->current()->items()->sum('qty');
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function render()
    {
        return view('livewire.shared.cart-badge');
    }
}
