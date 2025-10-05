<?php

namespace App\Livewire\Shared;

use App\Services\CartService;
use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\Store;

class CartBadge extends Component
{
    public int $count = 0;
    public ?int $storeId = null;
    protected CartService $cartService;

    protected $listeners = [
        'cart.updated' => 'refreshCount',
        'cart.item-added' => 'refreshCount',
        'cart.item-removed' => 'refreshCount',
        'cart.cleared' => 'refreshCount',
    ];

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount(Request $request): void
    {
        $this->storeId = $request->store?->id;

        // Check if cart is expired and clear if necessary
        $this->cartService->checkAndClearExpiredCart($this->storeId);

        $this->count = $this->fetchCount($this->cartService);
    }

    public function refreshCount(): void
    {
        // Check if cart is expired and clear if necessary
        $this->cartService->checkAndClearExpiredCart($this->storeId);

        $this->count = $this->fetchCount($this->cartService);
    }

    private function fetchCount(): int
    {
        try {
            return $this->cartService->getCartCount($this->storeId);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function render()
    {
        return view('livewire.shared.cart-badge');
    }
}
