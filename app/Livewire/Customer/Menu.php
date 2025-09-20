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

    public array $config = [];
    public ?int $configItemId = null;

    public function startConfigure(int $menuItemId): void
    {
        $item = MenuItem::findOrFail($menuItemId);
        $this->configItemId = $menuItemId;

        // Reset configuration for new item
        $this->config = [
            'type' => $item->type ?? 'ala_carte',
            'options' => [],
            'addons' => [],
        ];

        $this->dispatch('open-config');
    }

    public function resetConfig(): void
    {
        $this->config = [
            'type' => 'ala_carte',
            'options' => [],
            'addons' => [],
        ];
        $this->configItemId = null;
    }

    public function addConfiguredToCart(int $qty = 1): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        $item = MenuItem::findOrFail((int)$this->configItemId);
        $selections = [];

        // Add selected options (if any)
        if (!empty($this->config['options'])) {
            $selections['options'] = $this->config['options'];
        }

        // Add selected addons (if any)
        if (!empty($this->config['addons'])) {
            $selections['addons'] = $this->config['addons'];
        }

        $this->cartService->add($item->id, $qty, $selections);
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Added to cart']);
        $this->dispatch('close-config');
        $this->config = [];
        $this->configItemId = null;
    }

    public function addToCart(int $menuItemId): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        // For items with options/addons, open configurator instead
        $item = MenuItem::findOrFail($menuItemId);
        if (!empty($item->options) || !empty($item->addons)) {
            $this->startConfigure($menuItemId);
            return;
        }
        $this->cartService->add($menuItemId, 1, []);
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

        return $query->get(['id', 'category_id', 'name', 'description', 'price', 'base_price', 'type', 'options', 'addons', 'image_path', 'is_active', 'stock', 'tag']);
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

    public function getTotalPrice(): float
    {
        if (!$this->configItemId) {
            return 0.0;
        }

        $item = MenuItem::find($this->configItemId);
        if (!$item) {
            return 0.0;
        }

        $subtotal = 0.0;

        // Base price
        if ($item->type === 'set') {
            $subtotal += (float) ($item->base_price ?? 0);
        } else {
            $subtotal += (float) ($item->price ?? 0);
        }

        // Add selected option price (if any) - Options don't have individual pricing
        // Options are just choices, no additional cost

        // Add selected addon prices (if any)
        if (!empty($this->config['addons'])) {
            foreach ($this->config['addons'] as $addonGroup) {
                if (isset($addonGroup['options'])) {
                    foreach ($addonGroup['options'] as $option) {
                        $subtotal += (float) ($option['price'] ?? 0);
                    }
                }
            }
        }

        return round($subtotal, 2);
    }

    public function getTotalWithTax(): float
    {
        $subtotal = $this->getTotalPrice();
        $tax = $subtotal * 0.1;
        return round($subtotal + $tax, 2);
    }

    public function getCartTotalsProperty(): array
    {
        $cart = $this->cartService->current();
        return $this->cartService->getTotals($cart);
    }

    public function increment(int $menuItemId, ?int $lineId = null): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        if ($lineId) {
            $this->cartService->incrementLine($lineId);
        } else {
            $this->cartService->increment($menuItemId);
        }
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Quantity updated']);
    }

    public function decrement(int $menuItemId, ?int $lineId = null): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        if ($lineId) {
            $this->cartService->decrementLine($lineId);
        } else {
            $this->cartService->decrement($menuItemId);
        }
        $this->dispatch('flash', ['type' => 'success', 'message' => 'Quantity updated']);
    }

    public function remove(int $menuItemId, ?int $lineId = null): void
    {
        if (!Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        if ($lineId) {
            $this->cartService->removeLine($lineId);
        } else {
            $this->cartService->remove($menuItemId);
        }
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
