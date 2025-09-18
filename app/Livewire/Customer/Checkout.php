<?php

namespace App\Livewire\Customer;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.customer')]
class Checkout extends Component
{
    public bool $deliver = false;
    public ?int $addressId = null;
    public string $notes = '';

    protected CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function mount(): void
    {
        if (Auth::check()) {
            $default = Auth::user()->defaultAddress();
            $this->addressId = $default?->id;
        }
    }

    public function updatedDeliver(): void
    {
        if ($this->deliver && Auth::check()) {
            $this->addressId = Auth::user()->defaultAddress()?->id;
        }
    }

    public function getCartLinesProperty(): array
    {
        $cart = $this->cartService->current();
        return $this->cartService->getLines($cart);
    }

    public function getCartTotalsProperty(): array
    {
        $cart = $this->cartService->current();
        return $this->cartService->getTotals($cart);
    }

    public function render()
    {
        return view('livewire.customer.checkout');
    }
}
