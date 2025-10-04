<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\CartService;
use App\Models\CartItem;
use App\Models\Store;
use Illuminate\Http\Request;

#[Layout('layouts.customer')]
class Cart extends Component
{
    protected CartService $cartService;
    public ?Store $store = null;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount(Request $request)
    {
        $this->store = $request->store;
    }

    public function increment(int $id): void
    {
        $this->cartService->incrementLine($id);
    }

    public function decrement(int $id): void
    {
        $this->cartService->decrementLine($id);
    }

    public function remove(int $id): void
    {
        $this->cartService->removeLine($id);
    }

    public function clear(): void
    {
        $this->cartService->clear();
    }

    public function getLinesProperty(): array
    {
        $cart = $this->cartService->current();
        return $cart->items()->with('menuItem')->get()->map(function (CartItem $line) {
            return [
                'id' => $line->id,
                'menu_item_id' => $line->menu_item_id,
                'name' => $line->menuItem->name,
                'price' => (float) $line->unit_price,
                'qty' => $line->qty,
                'image_path' => $line->menuItem->image_path,
                'selections' => $line->selections,
                'line_total' => $line->qty * $line->unit_price,
            ];
        })->all();
    }

    public function getTotalsProperty(): array
    {
        $subtotal = collect($this->lines)->reduce(fn($c, $l) => $c + ($l['price'] * $l['qty']), 0.0);
        $tax = round($subtotal * 0.1, 2);
        $total = round($subtotal + $tax, 2);
        return compact('subtotal', 'tax', 'total');
    }

    public function render()
    {
        return view('livewire.customer.cart');
    }
}
