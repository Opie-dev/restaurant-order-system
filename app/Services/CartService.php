<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getCartKey(?int $storeId = null): string
    {
        if (Auth::check()) {
            return "cart_user_" . Auth::id() . "_store_" . $storeId;
        }
        return "cart_guest_store_" . $storeId;
    }

    public function getCartTimerKey(?int $storeId = null): string
    {
        if (Auth::check()) {
            return "cart_timer_user_" . Auth::id() . "_store_" . $storeId;
        }
        return "cart_timer_guest_store_" . $storeId;
    }

    public function getCart(?int $storeId = null): array
    {
        return session($this->getCartKey($storeId), []);
    }

    public function setCart(array $cart, ?int $storeId = null): void
    {
        session([$this->getCartKey($storeId) => $cart]);
        // Reset timer when cart is updated (only if cart has items)
        if (!empty($cart)) {
            $this->resetCartTimer($storeId);
        }
    }

    public function getCartTimer(?int $storeId = null): ?int
    {
        return session($this->getCartTimerKey($storeId));
    }

    public function resetCartTimer(?int $storeId = null): void
    {
        // Set timer to 15 minutes (900 seconds) from now
        session([$this->getCartTimerKey($storeId) => time() + 900]);
    }

    public function isCartExpired(?int $storeId = null): bool
    {
        $timer = $this->getCartTimer($storeId);
        // If there is no timer, do not treat as expired. This can happen after
        // session regeneration (e.g., login). Callers should decide whether to
        // restart the timer based on cart contents.
        if (!$timer) {
            return false;
        }
        return time() > $timer;
    }

    public function getCartTimeRemaining(?int $storeId = null): int
    {
        $timer = $this->getCartTimer($storeId);
        if (!$timer) {
            return 0;
        }
        return max(0, $timer - time());
    }

    public function add(int $menuItemId, int $qty = 1, array $selections = [], ?int $storeId = null): void
    {
        $item = MenuItem::where('is_active', true)->findOrFail($menuItemId);
        $cart = $this->getCart($storeId);

        // Find existing line with same selections
        $existingIndex = null;
        foreach ($cart as $index => $line) {
            if (
                $line['menu_item_id'] == $menuItemId &&
                json_encode($line['selections'] ?? []) === json_encode($selections)
            ) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $currentQty = (int) $cart[$existingIndex]['qty'];
            $desiredQty = $currentQty + $qty;
        } else {
            $currentQty = 0;
            $desiredQty = $qty;
        }

        // If stock is defined, do not allow exceeding stock
        if (isset($item->stock)) {
            $availableStock = max(0, (int) $item->stock);

            // If creating a new line and there is no stock, do nothing
            if ($existingIndex === null && $availableStock <= 0) {
                return;
            }

            // If line exists and already at or above stock, keep as-is (no-op)
            if ($existingIndex !== null && $currentQty >= $availableStock) {
                return;
            }

            $newQty = min($desiredQty, max(1, $availableStock));
        } else {
            $newQty = $desiredQty;
        }

        // Ensure minimum of 1 for existing lines; for new lines, ensure > 0
        if ($existingIndex !== null) {
            $cart[$existingIndex]['qty'] = max(1, (int) $newQty);
        } else {
            if ($newQty <= 0) {
                return;
            }
            $cart[] = [
                'menu_item_id' => $menuItemId,
                'qty' => (int) $newQty,
                'unit_price' => $this->computeUnitPrice($item, $selections),
                'selections' => !empty($selections) ? $selections : null,
            ];
        }

        $this->setCart($cart, $storeId);

        // Initialize timer if this is the first item
        if (count($cart) === 1 && $existingIndex === null) {
            $this->resetCartTimer($storeId);
        }
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

    public function setQty(int $menuItemId, int $qty, ?int $storeId = null): void
    {
        $cart = $this->getCart($storeId);
        foreach ($cart as $index => $line) {
            if ($line['menu_item_id'] == $menuItemId) {
                $item = MenuItem::find($menuItemId);
                if ($item && isset($item->stock)) {
                    $qty = min($qty, max(0, (int) $item->stock));
                }
                $cart[$index]['qty'] = max(1, $qty);
                $this->setCart($cart, $storeId);
                return;
            }
        }
    }

    public function increment(int $menuItemId, ?int $storeId = null): void
    {
        $this->add($menuItemId, 1, [], $storeId);
    }

    public function decrement(int $menuItemId, ?int $storeId = null): void
    {
        $cart = $this->getCart($storeId);
        foreach ($cart as $index => $line) {
            if ($line['menu_item_id'] == $menuItemId) {
                $cart[$index]['qty'] = max(1, $line['qty'] - 1);
                $this->setCart($cart, $storeId);
                return;
            }
        }
    }

    public function remove(int $menuItemId, ?int $storeId = null): void
    {
        $cart = $this->getCart($storeId);
        $cart = array_filter($cart, function ($line) use ($menuItemId) {
            return $line['menu_item_id'] != $menuItemId;
        });
        $this->setCart(array_values($cart), $storeId);
    }

    public function clear(?int $storeId = null): void
    {
        $this->setCart([], $storeId);
    }

    public function getLines(?int $storeId = null): array
    {
        $cart = $this->getCart($storeId);
        $lines = [];

        foreach ($cart as $line) {
            $item = MenuItem::find($line['menu_item_id']);
            if ($item) {
                $lines[] = [
                    'id' => $line['menu_item_id'], // Use menu_item_id as identifier
                    'item' => $item,
                    'qty' => $line['qty'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['qty'] * $line['unit_price'],
                    'selections' => $line['selections'] ?? null,
                ];
            }
        }

        return $lines;
    }

    public function incrementLine(int $lineId, ?int $storeId = null): void
    {
        $cart = $this->getCart($storeId);
        foreach ($cart as $index => $line) {
            if ($line['menu_item_id'] == $lineId) {
                $item = MenuItem::find($lineId);
                if ($item && isset($item->stock)) {
                    $cap = max(0, (int) $item->stock);
                    if ($line['qty'] >= $cap) return;
                }
                $cart[$index]['qty'] = $line['qty'] + 1;
                $this->setCart($cart, $storeId);
                return;
            }
        }
    }

    public function decrementLine(int $lineId, ?int $storeId = null): void
    {
        $cart = $this->getCart($storeId);
        foreach ($cart as $index => $line) {
            if ($line['menu_item_id'] == $lineId) {
                $cart[$index]['qty'] = max(1, $line['qty'] - 1);
                $this->setCart($cart, $storeId);
                return;
            }
        }
    }

    public function removeLine(int $lineId, ?int $storeId = null): void
    {
        $cart = $this->getCart($storeId);
        $cart = array_filter($cart, function ($line) use ($lineId) {
            return $line['menu_item_id'] != $lineId;
        });
        $this->setCart(array_values($cart), $storeId);
    }

    public function getTotals(?int $storeId = null): array
    {
        $cart = $this->getCart($storeId);
        $subtotal = 0.0;

        foreach ($cart as $line) {
            $subtotal += $line['qty'] * $line['unit_price'];
        }

        // Get tax rate from store
        $taxRate = 0.0;
        if ($storeId) {
            $store = Store::find($storeId);
            $taxRate = $store ? (float) $store->tax_rate : 0.0;
        }

        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_rate' => $taxRate,
            'total' => $total,
        ];
    }

    public function getCartCount(?int $storeId = null): int
    {
        $cart = $this->getCart($storeId);
        $count = 0;
        foreach ($cart as $line) {
            $count += $line['qty'];
        }
        return $count;
    }

    public function checkAndClearExpiredCart(?int $storeId = null): bool
    {
        $timer = $this->getCartTimer($storeId);
        $cart = $this->getCart($storeId);

        // If timer is missing but there are items, restart timer (common after login)
        if (!$timer && !empty($cart)) {
            $this->resetCartTimer($storeId);
            return false;
        }

        if ($this->isCartExpired($storeId)) {
            $this->clear($storeId);
            return true; // Cart was cleared
        }
        return false; // Cart is still valid
    }

    public function restartTimer(?int $storeId = null): void
    {
        $cart = $this->getCart($storeId);
        if (!empty($cart)) {
            $this->resetCartTimer($storeId);
        }
    }
}
