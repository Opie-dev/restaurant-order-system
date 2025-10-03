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
        $storeId = session('current_store_id');
        if (Auth::check()) {
            return Cart::firstOrCreate([
                'user_id' => Auth::id(),
                'store_id' => $storeId,
            ]);
        }
        $token = session()->get('guest_cart_token');
        if (!$token) {
            $token = Str::uuid()->toString();
            session()->put('guest_cart_token', $token);
        }
        return Cart::firstOrCreate([
            'guest_token' => $token,
            'store_id' => $storeId,
        ]);
    }

    public function add(int $menuItemId, int $qty = 1, array $selections = []): void
    {
        $item = MenuItem::where('is_active', true)->findOrFail($menuItemId);
        $cart = $this->current();
        // Find existing line with same selections; else create new
        $line = CartItem::where('cart_id', $cart->id)
            ->where('menu_item_id', $item->id)
            ->where(function ($q) use ($selections) {
                if (empty($selections)) {
                    $q->whereNull('selections');
                } else {
                    $q->where('selections', json_encode($selections));
                }
            })->first();

        if (!$line) {
            $line = new CartItem([
                'cart_id' => $cart->id,
                'menu_item_id' => $item->id,
                'qty' => 0,
            ]);
            $line->selections = !empty($selections) ? $selections : null;
            $line->unit_price = $this->computeUnitPrice($item, $selections);
        }

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

    private function computeUnitPrice(MenuItem $item, array $selections): float
    {
        // Base price depends on type
        $base = ($item->type === 'set')
            ? (float) ($item->base_price ?? 0)
            : (float) ($item->price ?? 0);

        // Options have no price; only addons add cost
        $addonTotal = 0.0;
        foreach (($selections['addons'] ?? []) as $group) {
            foreach (($group['options'] ?? []) as $opt) {
                $addonTotal += (float) ($opt['price'] ?? 0);
            }
        }

        return round($base + $addonTotal, 2);
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
                'id' => $item->id,
                'item' => $item->menuItem,
                'qty' => $item->qty,
                'unit_price' => $item->unit_price,
                'line_total' => $item->qty * $item->unit_price,
                'selections' => $item->selections ?? null,
            ];
        })->toArray();
    }

    public function incrementLine(int $lineId): void
    {
        $cart = $this->current();
        $line = CartItem::where('cart_id', $cart->id)->where('id', $lineId)->with('menuItem')->first();
        if (!$line) return;
        $item = $line->menuItem;
        if ($item && isset($item->stock)) {
            $cap = max(0, (int) $item->stock);
            if ($line->qty >= $cap) return;
        }
        $line->qty = $line->qty + 1;
        $line->save();
    }

    public function decrementLine(int $lineId): void
    {
        $cart = $this->current();
        $line = CartItem::where('cart_id', $cart->id)->where('id', $lineId)->first();
        if (!$line) return;
        $line->qty = max(1, $line->qty - 1);
        $line->save();
    }

    public function removeLine(int $lineId): void
    {
        $cart = $this->current();
        CartItem::where('cart_id', $cart->id)->where('id', $lineId)->delete();
    }

    public function getTotals(Cart $cart): array
    {
        $subtotal = (float) ($cart->items()
            ->selectRaw('SUM(qty * unit_price) as subtotal')
            ->value('subtotal') ?? 0);
        $tax = $subtotal * 0.08; // 8% tax
        $total = $subtotal + $tax;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }
}
