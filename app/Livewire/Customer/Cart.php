<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\CartService;
use App\Models\CartItem;

#[Layout('layouts.app')]
class Cart extends Component
{
    public function increment(int $id): void
    {
        app(CartService::class)->increment($id);
    }

    public function decrement(int $id): void
    {
        app(CartService::class)->decrement($id);
    }

    public function remove(int $id): void
    {
        app(CartService::class)->remove($id);
    }

    public function clear(): void
    {
        app(CartService::class)->clear();
    }

    public function getLinesProperty(): array
    {
        $cart = app(CartService::class)->current();
        return $cart->items()->with('menuItem')->get()->map(function (CartItem $line) {
            return [
                'id' => $line->menu_item_id,
                'name' => $line->menuItem->name,
                'price' => (float) $line->unit_price,
                'qty' => $line->qty,
                'image_path' => $line->menuItem->image_path,
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
