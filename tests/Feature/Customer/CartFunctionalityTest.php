<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Cart;
use App\Models\CartItem;
use Livewire\Livewire;
use App\Livewire\Customer\Menu;
use App\Livewire\Customer\Cart as CartComponent;

describe('Cart Functionality', function () {
    beforeEach(function () {
        $this->store = Store::factory()->create(['is_active' => true]);
        $this->category = Category::factory()->create(['store_id' => $this->store->id]);
        $this->menuItem = MenuItem::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'price' => 12.99
        ]);
    });

    describe('Add to Cart - Guest User', function () {
        it('can add item to cart without options', function () {
            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 2)
                ->assertDispatched('cart-updated');

            $cartItem = CartItem::where('menu_item_id', $this->menuItem->id)->first();
            expect($cartItem)->not->toBeNull();
            expect($cartItem->qty)->toBe(2);
            expect($cartItem->unit_price)->toBe(12.99);
        });

        it('can add item to cart with options', function () {
            $this->menuItem->update([
                'options' => [
                    ['name' => 'Size', 'required' => true, 'choices' => [
                        ['name' => 'Small', 'price' => 0],
                        ['name' => 'Large', 'price' => 3.00]
                    ]],
                    ['name' => 'Extra Toppings', 'required' => false, 'choices' => [
                        ['name' => 'Extra Cheese', 'price' => 2.00]
                    ]]
                ]
            ]);

            $selections = [
                'Size' => 'Large',
                'Extra Toppings' => ['Extra Cheese']
            ];

            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 1, $selections)
                ->assertDispatched('cart-updated');

            $cartItem = CartItem::where('menu_item_id', $this->menuItem->id)->first();
            expect($cartItem)->not->toBeNull();
            expect($cartItem->selections)->toBe($selections);
            expect($cartItem->unit_price)->toBe(12.99 + 3.00 + 2.00); // Base + Large + Extra Cheese
        });

        it('can add item to cart with addons', function () {
            $this->menuItem->update([
                'addons' => [
                    ['name' => 'Side Salad', 'price' => 4.99],
                    ['name' => 'Drink', 'price' => 2.99]
                ]
            ]);

            $addons = ['Side Salad', 'Drink'];

            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 1, [], $addons)
                ->assertDispatched('cart-updated');

            $cartItem = CartItem::where('menu_item_id', $this->menuItem->id)->first();
            expect($cartItem)->not->toBeNull();
            expect($cartItem->addons)->toBe($addons);
            expect($cartItem->unit_price)->toBe(12.99 + 4.99 + 2.99); // Base + Side Salad + Drink
        });

        it('updates quantity when adding same item', function () {
            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 2)
                ->call('addToCart', $this->menuItem->id, 1)
                ->assertDispatched('cart-updated');

            $cartItem = CartItem::where('menu_item_id', $this->menuItem->id)->first();
            expect($cartItem->qty)->toBe(3);
        });

        it('creates separate cart items for different selections', function () {
            $this->menuItem->update([
                'options' => [
                    ['name' => 'Size', 'required' => true, 'choices' => [
                        ['name' => 'Small', 'price' => 0],
                        ['name' => 'Large', 'price' => 3.00]
                    ]]
                ]
            ]);

            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 1, ['Size' => 'Small'])
                ->call('addToCart', $this->menuItem->id, 1, ['Size' => 'Large'])
                ->assertDispatched('cart-updated');

            expect(CartItem::where('menu_item_id', $this->menuItem->id)->count())->toBe(2);
        });

        it('cannot add inactive menu item to cart', function () {
            $this->menuItem->update(['is_active' => false]);

            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 1)
                ->assertDispatched('item-not-available');

            expect(CartItem::where('menu_item_id', $this->menuItem->id)->count())->toBe(0);
        });

        it('cannot add item from inactive store to cart', function () {
            $this->store->update(['is_active' => false]);

            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 1)
                ->assertDispatched('store-not-available');

            expect(CartItem::where('menu_item_id', $this->menuItem->id)->count())->toBe(0);
        });
    });

    describe('Add to Cart - Authenticated User', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);
        });

        it('can add item to cart as authenticated customer', function () {
            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 2)
                ->assertDispatched('cart-updated');

            $cartItem = CartItem::where('menu_item_id', $this->menuItem->id)
                ->where('user_id', $this->customer->id)
                ->first();

            expect($cartItem)->not->toBeNull();
            expect($cartItem->qty)->toBe(2);
        });

        it('persists cart items for authenticated user', function () {
            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 2);

            // Simulate page refresh/login
            auth()->logout();
            auth()->login($this->customer);

            $cartItem = CartItem::where('menu_item_id', $this->menuItem->id)
                ->where('user_id', $this->customer->id)
                ->first();

            expect($cartItem)->not->toBeNull();
            expect($cartItem->qty)->toBe(2);
        });

        it('merges session cart with user cart on login', function () {
            // Add item as guest
            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 2);

            // Login as customer
            auth()->login($this->customer);

            // Check if cart items are merged
            $cartItem = CartItem::where('menu_item_id', $this->menuItem->id)
                ->where('user_id', $this->customer->id)
                ->first();

            expect($cartItem)->not->toBeNull();
            expect($cartItem->qty)->toBe(2);
        });
    });

    describe('Cart Management', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);

            $this->cartItem = CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem->id,
                'qty' => 2,
                'unit_price' => 12.99
            ]);
        });

        it('displays cart items', function () {
            Livewire::test(CartComponent::class)
                ->assertSee($this->menuItem->name)
                ->assertSee('2')
                ->assertSee('$12.99');
        });

        it('can update item quantity', function () {
            Livewire::test(CartComponent::class)
                ->call('updateQuantity', $this->cartItem->id, 3)
                ->assertHasNoErrors();

            $this->cartItem->refresh();
            expect($this->cartItem->qty)->toBe(3);
        });

        it('can remove item from cart', function () {
            Livewire::test(CartComponent::class)
                ->call('removeItem', $this->cartItem->id)
                ->assertHasNoErrors();

            expect(CartItem::find($this->cartItem->id))->toBeNull();
        });

        it('can clear entire cart', function () {
            CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem->id
            ]);

            Livewire::test(CartComponent::class)
                ->call('clearCart')
                ->assertHasNoErrors();

            expect(CartItem::where('user_id', $this->customer->id)->count())->toBe(0);
        });

        it('calculates total correctly', function () {
            $cartItem2 = CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem->id,
                'qty' => 1,
                'unit_price' => 15.99
            ]);

            Livewire::test(CartComponent::class)
                ->assertSee('$' . number_format((12.99 * 2) + (15.99 * 1), 2));
        });

        it('applies tax correctly', function () {
            $subtotal = 12.99 * 2;
            $taxRate = 0.08; // 8% tax
            $expectedTax = $subtotal * $taxRate;

            Livewire::test(CartComponent::class)
                ->assertSee('$' . number_format($expectedTax, 2));
        });
    });

    describe('Cart Validation', function () {
        it('validates required options are selected', function () {
            $this->menuItem->update([
                'options' => [
                    ['name' => 'Size', 'required' => true, 'choices' => [
                        ['name' => 'Small', 'price' => 0],
                        ['name' => 'Large', 'price' => 3.00]
                    ]]
                ]
            ]);

            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 1, []) // No size selected
                ->assertDispatched('validation-error', 'Size is required');

            expect(CartItem::where('menu_item_id', $this->menuItem->id)->count())->toBe(0);
        });

        it('validates minimum quantity', function () {
            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 0)
                ->assertDispatched('validation-error', 'Quantity must be at least 1');

            expect(CartItem::where('menu_item_id', $this->menuItem->id)->count())->toBe(0);
        });

        it('validates maximum quantity', function () {
            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 100)
                ->assertDispatched('validation-error', 'Maximum quantity exceeded');

            expect(CartItem::where('menu_item_id', $this->menuItem->id)->count())->toBe(0);
        });

        it('validates item availability', function () {
            $this->menuItem->update(['is_active' => false]);

            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 1)
                ->assertDispatched('item-not-available');

            expect(CartItem::where('menu_item_id', $this->menuItem->id)->count())->toBe(0);
        });
    });

    describe('Cart Persistence', function () {
        it('persists cart for guest users in session', function () {
            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 2);

            // Simulate new request with same session
            $this->get('/cart')
                ->assertSee($this->menuItem->name)
                ->assertSee('2');
        });

        it('clears cart after successful checkout', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            CartItem::factory()->create([
                'user_id' => $customer->id,
                'menu_item_id' => $this->menuItem->id,
                'qty' => 2
            ]);

            // Simulate successful checkout
            $this->post('/checkout', [
                'payment_method' => 'card',
                'delivery_address' => '123 Main St, Test City, Test State, 12345'
            ]);

            expect(CartItem::where('user_id', $customer->id)->count())->toBe(0);
        });

        it('maintains cart when switching between stores', function () {
            $store2 = Store::factory()->create(['is_active' => true]);
            $category2 = Category::factory()->create(['store_id' => $store2->id]);
            $menuItem2 = MenuItem::factory()->create([
                'store_id' => $store2->id,
                'category_id' => $category2->id,
                'is_active' => true
            ]);

            Livewire::test(Menu::class)
                ->call('addToCart', $this->menuItem->id, 1)
                ->call('addToCart', $menuItem2->id, 1);

            expect(CartItem::count())->toBe(2);
        });
    });
});
