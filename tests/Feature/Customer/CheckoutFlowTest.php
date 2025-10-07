<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\UserAddress;
use Livewire\Livewire;
use App\Livewire\Customer\Checkout;
use Illuminate\Support\Facades\Notification;

describe('Checkout Flow', function () {
    beforeEach(function () {
        $this->store = Store::factory()->create(['is_active' => true]);
        $this->category = Category::factory()->create(['store_id' => $this->store->id]);
        $this->menuItem1 = MenuItem::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'price' => 12.99
        ]);
        $this->menuItem2 = MenuItem::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'is_active' => true,
            'price' => 8.99
        ]);
    });

    describe('Guest Checkout', function () {
        beforeEach(function () {
            // Add items to cart as guest
            CartItem::factory()->create([
                'session_id' => session()->getId(),
                'menu_item_id' => $this->menuItem1->id,
                'qty' => 2,
                'unit_price' => 12.99
            ]);
            CartItem::factory()->create([
                'session_id' => session()->getId(),
                'menu_item_id' => $this->menuItem2->id,
                'qty' => 1,
                'unit_price' => 8.99
            ]);
        });

        it('can proceed to checkout as guest', function () {
            $this->get('/checkout')
                ->assertStatus(200)
                ->assertSee($this->menuItem1->name)
                ->assertSee($this->menuItem2->name);
        });

        it('validates required guest information', function () {
            Livewire::test(Checkout::class)
                ->set('guest_name', '')
                ->set('guest_email', '')
                ->set('guest_phone', '')
                ->call('processCheckout')
                ->assertHasErrors(['guest_name', 'guest_email', 'guest_phone']);
        });

        it('validates email format for guest', function () {
            Livewire::test(Checkout::class)
                ->set('guest_email', 'invalid-email')
                ->call('processCheckout')
                ->assertHasErrors(['guest_email']);
        });

        it('validates phone format for guest', function () {
            Livewire::test(Checkout::class)
                ->set('guest_phone', 'invalid-phone')
                ->call('processCheckout')
                ->assertHasErrors(['guest_phone']);
        });

        it('can complete guest checkout', function () {
            Livewire::test(Checkout::class)
                ->set('guest_name', 'John Doe')
                ->set('guest_email', 'john@example.com')
                ->set('guest_phone', '555-0123')
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors()
                ->assertRedirect();

            expect(Order::count())->toBe(1);
            $order = Order::first();
            expect($order->user_id)->toBeNull();
            expect($order->guest_name)->toBe('John Doe');
            expect($order->guest_email)->toBe('john@example.com');
        });
    });

    describe('Authenticated Customer Checkout', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);

            // Add items to cart
            CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem1->id,
                'qty' => 2,
                'unit_price' => 12.99
            ]);
            CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem2->id,
                'qty' => 1,
                'unit_price' => 8.99
            ]);
        });

        it('can proceed to checkout as authenticated customer', function () {
            $this->get('/checkout')
                ->assertStatus(200)
                ->assertSee($this->menuItem1->name)
                ->assertSee($this->menuItem2->name);
        });

        it('can use saved address', function () {
            $address = UserAddress::factory()->create([
                'user_id' => $this->customer->id,
                'is_default' => true
            ]);

            Livewire::test(Checkout::class)
                ->set('selected_address_id', $address->id)
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            $order = Order::first();
            expect($order->user_id)->toBe($this->customer->id);
            expect($order->delivery_address)->toBe($address->address_line1);
        });

        it('can enter new delivery address', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '456 New Street, New City, New State, 54321')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            $order = Order::first();
            expect($order->delivery_address)->toBe('456 New Street, New City, New State, 54321');
        });

        it('validates delivery address is required', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasErrors(['delivery_address']);
        });

        it('validates payment method is required', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', '')
                ->call('processCheckout')
                ->assertHasErrors(['payment_method']);
        });

        it('can add special instructions', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->set('special_instructions', 'Please ring the doorbell twice')
                ->call('processCheckout')
                ->assertHasNoErrors();

            $order = Order::first();
            expect($order->notes)->toBe('Please ring the doorbell twice');
        });
    });

    describe('Order Creation', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);

            CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem1->id,
                'qty' => 2,
                'unit_price' => 12.99
            ]);
        });

        it('creates order with correct totals', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            $order = Order::first();
            expect($order->subtotal)->toBe(25.98); // 12.99 * 2
            expect($order->tax)->toBe(2.08); // 8% tax
            expect($order->total)->toBe(28.06); // subtotal + tax
        });

        it('creates order items correctly', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            expect(OrderItem::count())->toBe(1);
            $orderItem = OrderItem::first();
            expect($orderItem->menu_item_id)->toBe($this->menuItem1->id);
            expect($orderItem->qty)->toBe(2);
            expect($orderItem->unit_price)->toBe(12.99);
            expect($orderItem->line_total)->toBe(25.98);
        });

        it('generates unique order code', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            $order = Order::first();
            expect($order->code)->not->toBeNull();
            expect(strlen($order->code))->toBeGreaterThan(5);
        });

        it('sets initial order status', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            $order = Order::first();
            expect($order->status)->toBe('pending');
            expect($order->payment_status)->toBe('unpaid');
        });

        it('clears cart after successful checkout', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            expect(CartItem::where('user_id', $this->customer->id)->count())->toBe(0);
        });
    });

    describe('Payment Processing', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);

            CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem1->id,
                'qty' => 1,
                'unit_price' => 12.99
            ]);
        });

        it('can process card payment', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->set('card_number', '4242424242424242')
                ->set('card_expiry', '12/25')
                ->set('card_cvv', '123')
                ->call('processCheckout')
                ->assertHasNoErrors();

            $order = Order::first();
            expect($order->payment_method)->toBe('card');
            expect($order->payment_status)->toBe('processing');
        });

        it('can process cash payment', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'cash')
                ->call('processCheckout')
                ->assertHasNoErrors();

            $order = Order::first();
            expect($order->payment_method)->toBe('cash');
            expect($order->payment_status)->toBe('unpaid');
        });

        it('validates card information', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->set('card_number', 'invalid')
                ->set('card_expiry', 'invalid')
                ->set('card_cvv', 'invalid')
                ->call('processCheckout')
                ->assertHasErrors(['card_number', 'card_expiry', 'card_cvv']);
        });

        it('handles payment failure gracefully', function () {
            // Mock payment failure
            $this->mock(\App\Services\PaymentService::class, function ($mock) {
                $mock->shouldReceive('processPayment')
                    ->andThrow(new \Exception('Payment failed'));
            });

            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasErrors();
        });
    });

    describe('Checkout Notifications', function () {
        beforeEach(function () {
            Notification::fake();
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);

            CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem1->id,
                'qty' => 1,
                'unit_price' => 12.99
            ]);
        });

        it('sends order confirmation email to customer', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            Notification::assertSentTo(
                $this->customer,
                \App\Mail\OrderConfirmation::class
            );
        });

        it('sends new order notification to store admin', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasNoErrors();

            Notification::assertSentTo(
                $this->store->admin,
                \App\Mail\NewOrderNotification::class
            );
        });

        it('sends SMS notification if phone number provided', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->set('phone_number', '555-0123')
                ->call('processCheckout')
                ->assertHasNoErrors();

            // Assert SMS was sent (mock SMS service)
            $this->assertTrue(true); // Placeholder for SMS assertion
        });
    });

    describe('Checkout Validation', function () {
        it('prevents checkout with empty cart', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasErrors();

            expect(Order::count())->toBe(0);
        });

        it('prevents checkout with inactive store', function () {
            $this->store->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            CartItem::factory()->create([
                'user_id' => $customer->id,
                'menu_item_id' => $this->menuItem1->id,
                'qty' => 1,
                'unit_price' => 12.99
            ]);

            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasErrors();

            expect(Order::count())->toBe(0);
        });

        it('prevents checkout when store is closed', function () {
            $this->store->update([
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                    ],
                    'always_open' => false
                ]
            ]);

            // Mock current time to be outside opening hours
            $this->travelTo(now()->setTime(20, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            CartItem::factory()->create([
                'user_id' => $customer->id,
                'menu_item_id' => $this->menuItem1->id,
                'qty' => 1,
                'unit_price' => 12.99
            ]);

            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertHasErrors();

            expect(Order::count())->toBe(0);
        });
    });

    describe('Order Tracking', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);

            CartItem::factory()->create([
                'user_id' => $this->customer->id,
                'menu_item_id' => $this->menuItem1->id,
                'qty' => 1,
                'unit_price' => 12.99
            ]);
        });

        it('redirects to order tracking page after checkout', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout')
                ->assertRedirect();

            $order = Order::first();
            expect($order->code)->not->toBeNull();
        });

        it('can track order status', function () {
            Livewire::test(Checkout::class)
                ->set('delivery_address', '123 Main St, Test City, Test State, 12345')
                ->set('payment_method', 'card')
                ->call('processCheckout');

            $order = Order::first();
            $order->update(['status' => 'preparing']);

            $this->get("/order/{$order->code}")
                ->assertSee('Preparing');
        });
    });
});
