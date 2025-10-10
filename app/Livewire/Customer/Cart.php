<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\CartService;
use App\Models\CartItem;
use App\Models\Store;
use Illuminate\Http\Request;
use Livewire\Attributes\On;

#[Layout('layouts.customer')]
class Cart extends Component
{
    protected CartService $cartService;
    public ?Store $store = null;
    public int $timeRemaining = 0;
    public bool $cartExpired = false;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount(Request $request)
    {
        $this->store = $request->store;

        // Check if cart is expired and clear if necessary
        if ($this->cartService->checkAndClearExpiredCart($this->store?->id)) {
            $this->cartExpired = true;
            return;
        }

        // Restart timer when navigating to cart page
        $this->cartService->restartTimer($this->store?->id);

        // Update timer
        $this->updateTimer();
    }

    public function updateTimer(): void
    {
        $this->timeRemaining = $this->cartService->getCartTimeRemaining($this->store?->id);
        $this->cartExpired = $this->cartService->isCartExpired($this->store?->id);
    }

    #[On('timer-tick')]
    public function onTimerTick(): void
    {
        $this->updateTimer();

        if ($this->cartExpired) {
            // Cart has expired, dispatch event to refresh the page
            $this->dispatch('cart-expired');
        }
    }

    public function increment(int $id): void
    {
        $this->cartService->incrementLine($id, $this->store?->id);
        $this->updateTimer();
    }

    public function decrement(int $id): void
    {
        $this->cartService->decrementLine($id, $this->store?->id);
        $this->updateTimer();
    }

    public function remove(int $id): void
    {
        $this->cartService->removeLine($id, $this->store?->id);
        $this->updateTimer();
    }

    public function clear(): void
    {
        $this->cartService->clear($this->store?->id);
        $this->updateTimer();
    }

    public function getLinesProperty(): array
    {
        return $this->cartService->getLines($this->store?->id);
    }

    public function getTotalsProperty(): array
    {
        return $this->cartService->getTotals($this->store?->id);
    }

    public function render()
    {
        return view('livewire.customer.cart');
    }
}
