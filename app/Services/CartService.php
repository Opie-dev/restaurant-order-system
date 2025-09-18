<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    public function current(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }
        $token = session()->get('guest_cart_token');
        if (!$token) {
            $token = Str::uuid()->toString();
            session()->put('guest_cart_token', $token);
        }
        return Cart::firstOrCreate(['guest_token' => $token]);
    }

    public function add(int $menuItemId, int $qty = 1): void
    {
        $item = MenuItem::where('is_active', true)->findOrFail($menuItemId);
        $cart = $this->current();
        $line = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'menu_item_id' => $item->id,
        ]);
        $line->unit_price = $line->exists ? $line->unit_price : $item->price;

        $currentQty = (int) ($line->qty ?? 0);
        $desiredQty = $currentQty + $qty;

        // If stock is defined, do not allow exceeding stock and avoid dropping below current when stock is 0
        if (isset($item->stock)) {
            $availableStock = max(0, (int) $item->stock);

            // If creating a new line and there is no stock, do nothing
            if (!$line->exists && $availableStock <= 0) {
                return;
            }

            // If line exists and already at or above stock, keep as-is (no-op)
            if ($line->exists && $currentQty >= $availableStock) {
                return;
            }

            $newQty = min($desiredQty, max(1, $availableStock));
        } else {
            $newQty = $desiredQty;
        }

        // Ensure minimum of 1 for existing lines; for new lines, ensure > 0
        if ($line->exists) {
            $line->qty = max(1, (int) $newQty);
        } else {
            if ($newQty <= 0) {
                return;
            }
            $line->qty = (int) $newQty;
        }

        $line->save();
    }

    public function setQty(int $menuItemId, int $qty): void
    {
        $cart = $this->current();
        $line = CartItem::where('cart_id', $cart->id)->where('menu_item_id', $menuItemId)->first();
        if (!$line) {
            return;
        }
        if (isset($line->menuItem->stock)) {
            $qty = min($qty, max(0, (int) $line->menuItem->stock));
        }
        $line->qty = max(1, $qty);
        $line->save();
    }

    public function increment(int $menuItemId): void
    {
        $this->add($menuItemId, 1);
    }

    public function decrement(int $menuItemId): void
    {
        $cart = $this->current();
        $line = CartItem::where('cart_id', $cart->id)->where('menu_item_id', $menuItemId)->first();
        if (!$line) return;
        $line->qty = max(1, $line->qty - 1);
        $line->save();
    }

    public function remove(int $menuItemId): void
    {
        $cart = $this->current();
        CartItem::where('cart_id', $cart->id)->where('menu_item_id', $menuItemId)->delete();
    }

    public function clear(): void
    {
        $cart = $this->current();
        $cart->items()->delete();
    }

    public function getLines(Cart $cart): array
    {
        return $cart->items()->with('menuItem')->get()->map(function ($item) {
            return [
                'item' => $item->menuItem,
                'qty' => $item->qty,
                'unit_price' => $item->unit_price,
                'line_total' => $item->qty * $item->unit_price,
            ];
        })->toArray();
    }

    public function getTotals(Cart $cart): array
    {
        $subtotal = $cart->items()->sum(\DB::raw('qty * unit_price'));
        $tax = $subtotal * 0.08; // 8% tax
        $total = $subtotal + $tax;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }
}
