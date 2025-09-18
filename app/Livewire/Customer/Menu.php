<?php

namespace App\Livewire\Customer;

use App\Models\Category;
use App\Models\MenuItem;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.customer')]
class Menu extends Component
{
    public string $search = '';
    public ?int $categoryId = null;

    protected CartService $cartService;

    public function boot(CartService $cartService): void
    {
        $this->cartService = $cartService;
    }

    public function addToCart(int $menuItemId): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        $this->cartService->add($menuItemId, 1);
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Added to cart']);
    }

    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)->where('parent_id', null)->ordered()->get(['id', 'name']);
    }

    public function getItemsProperty()
    {
        $query = MenuItem::query()
            ->where('is_active', true)
            ->when($this->categoryId, function ($q) {
                $category = Category::find($this->categoryId);
                if ($category) {
                    $childIds = $category->children()->pluck('id');
                    return $q->where(function ($query) use ($category, $childIds) {
                        $query->where('category_id', $category->id)
                            ->orWhereIn('category_id', $childIds);
                    });
                }
                return $q;
            })
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            // In-stock items first, then by position
            ->orderByRaw('(CASE WHEN COALESCE(stock, 0) > 0 THEN 0 ELSE 1 END)')
            ->orderBy('position');

        return $query->get(['id', 'category_id', 'name', 'description', 'price', 'image_path', 'is_active', 'stock']);
    }

    public function render()
    {
        return view('livewire.customer.menu');
    }

    public function getCartCountProperty(): int
    {
        $cart = $this->cartService->current();
        return (int) $cart->items()->sum('qty');
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

    public function increment(int $menuItemId): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        // Check stock before trying to increment
        $cart = $this->cartService->current();
        $line = $cart->items()->where('menu_item_id', $menuItemId)->first();
        $item = $line?->menuItem ?? MenuItem::find($menuItemId);

        if ($item && isset($item->stock) && (int) $item->stock <= 0) {
            $this->dispatch('flash', ['type' => 'error', 'message' => 'Not available: out of stock']);
            return;
        }

        // If at stock cap, no-op with feedback
        if ($line && $item && isset($item->stock) && $line->qty >= (int) $item->stock) {
            $this->dispatch('flash', ['type' => 'error', 'message' => 'Reached available stock']);
            return;
        }

        $this->cartService->increment($menuItemId);
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Quantity updated']);
    }

    public function decrement(int $menuItemId): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }

        $cart = $this->cartService->current();
        $line = $cart->items()->where('menu_item_id', $menuItemId)->first();
        $item = $line?->menuItem ?? MenuItem::find($menuItemId);

        if (!$line) {
            $this->dispatch('flash', ['type' => 'error', 'message' => 'Item not found in cart']);
            return;
        }

        // If out of stock, do not allow decrement or increment actions
        if ($item && isset($item->stock) && (int) $item->stock <= 0) {
            $this->dispatch('flash', ['type' => 'error', 'message' => 'Not available: out of stock']);
            return;
        }

        if ($line->qty <= 1) {
            $this->dispatch('flash', ['type' => 'error', 'message' => 'Minimum quantity is 1. Use remove button to delete item.']);
            return;
        }

        $this->cartService->decrement($menuItemId);
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Quantity updated']);
    }

    public function remove(int $menuItemId): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        $this->cartService->remove($menuItemId);
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Item removed from cart']);
    }

    public function clear(): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        $this->cartService->clear();
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Cart cleared']);
    }

    public function proceedToCheckout(): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }

        $cart = $this->cartService->current();
        $cartCount = $cart->items()->sum('qty');

        if ($cartCount <= 0) {
            $this->dispatch('flash', ['type' => 'error', 'message' => 'Your cart is empty. Please add items before proceeding to checkout.']);
            return;
        }

        // Validate stock before proceeding
        $invalidNames = [];
        $overCapNames = [];
        $cart->loadMissing(['items.menuItem']);
        foreach ($cart->items as $line) {
            $item = $line->menuItem;
            if (!$item) {
                $invalidNames[] = 'Unknown item';
                continue;
            }
            if (isset($item->stock)) {
                $stock = (int) $item->stock;
                if ($stock <= 0) {
                    $invalidNames[] = $item->name;
                    continue;
                }
                if ($line->qty > $stock) {
                    $overCapNames[] = $item->name;
                }
            }
        }

        if (!empty($invalidNames)) {
            $this->dispatch('flash', ['type' => 'error', 'message' => 'Some items are no longer available: ' . implode(', ', array_slice($invalidNames, 0, 3)) . (count($invalidNames) > 3 ? '...' : '')]);
            return;
        }

        if (!empty($overCapNames)) {
            $this->dispatch('flash', ['type' => 'error', 'message' => 'Some items exceed available stock: ' . implode(', ', array_slice($overCapNames, 0, 3)) . (count($overCapNames) > 3 ? '...' : '') . '. Please adjust quantities.']);
            return;
        }

        // Redirect to checkout page
        $this->redirectRoute('checkout');
    }
}
