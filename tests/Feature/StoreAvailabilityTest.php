<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\CartItem;

describe('Store Availability', function () {
    beforeEach(function () {
        $this->merchant = User::factory()->create(['role' => 'admin']);
        $this->store = Store::factory()->create([
            'admin_id' => $this->merchant->id,
            'is_active' => true
        ]);
        $this->category = Category::factory()->create(['store_id' => $this->store->id]);
        $this->menuItem = MenuItem::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'is_active' => true
        ]);
    });

    describe('Store Active/Inactive Status', function () {
        it('shows menu items when store is active', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertStatus(200)
                ->assertSee($this->menuItem->name);
        });

        it('hides menu items when store is inactive', function () {
            $this->store->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertStatus(200)
                ->assertDontSee($this->menuItem->name);
        });

        it('shows store closed message when inactive', function () {
            $this->store->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee('Store is currently closed');
        });

        it('prevents adding items to cart when store is inactive', function () {
            $this->store->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->post('/cart/add', [
                'menu_item_id' => $this->menuItem->id,
                'qty' => 1
            ])->assertStatus(403);

            expect(CartItem::count())->toBe(0);
        });

        it('prevents checkout when store is inactive', function () {
            $this->store->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            CartItem::factory()->create([
                'user_id' => $customer->id,
                'menu_item_id' => $this->menuItem->id,
                'qty' => 1
            ]);

            $this->post('/checkout', [
                'delivery_address' => '123 Main St, Test City, Test State, 12345',
                'payment_method' => 'card'
            ])->assertStatus(403);

            expect(Order::count())->toBe(0);
        });
    });

    describe('Store Opening Hours', function () {
        beforeEach(function () {
            $this->store->update([
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                        ['day' => 'Tuesday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                        ['day' => 'Wednesday', 'enabled' => false, 'open' => '', 'close' => ''],
                        ['day' => 'Thursday', 'enabled' => true, 'open' => '10:00', 'close' => '18:00'],
                        ['day' => 'Friday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                        ['day' => 'Saturday', 'enabled' => true, 'open' => '10:00', 'close' => '16:00'],
                        ['day' => 'Sunday', 'enabled' => false, 'open' => '', 'close' => ''],
                    ],
                    'always_open' => false
                ]
            ]);
        });

        it('shows menu items when store is open', function () {
            // Mock current time to Monday 10:00 AM (within opening hours)
            $this->travelTo(now()->startOfWeek()->setTime(10, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee($this->menuItem->name);
        });

        it('hides menu items when store is closed', function () {
            // Mock current time to Monday 8:00 PM (outside opening hours)
            $this->travelTo(now()->startOfWeek()->setTime(20, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name);
        });

        it('shows next opening time when closed', function () {
            // Mock current time to Monday 8:00 PM
            $this->travelTo(now()->startOfWeek()->setTime(20, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee('Opens Tuesday at 09:00');
        });

        it('shows closed message on disabled days', function () {
            // Mock current time to Wednesday (disabled day)
            $this->travelTo(now()->startOfWeek()->addDays(2)->setTime(12, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee('Store is closed on Wednesdays');
        });

        it('prevents adding items to cart when store is closed', function () {
            // Mock current time to Monday 8:00 PM
            $this->travelTo(now()->startOfWeek()->setTime(20, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->post('/cart/add', [
                'menu_item_id' => $this->menuItem->id,
                'qty' => 1
            ])->assertStatus(403);

            expect(CartItem::count())->toBe(0);
        });

        it('prevents checkout when store is closed', function () {
            // Mock current time to Monday 8:00 PM
            $this->travelTo(now()->startOfWeek()->setTime(20, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            CartItem::factory()->create([
                'user_id' => $customer->id,
                'menu_item_id' => $this->menuItem->id,
                'qty' => 1
            ]);

            $this->post('/checkout', [
                'delivery_address' => '123 Main St, Test City, Test State, 12345',
                'payment_method' => 'card'
            ])->assertStatus(403);

            expect(Order::count())->toBe(0);
        });

        it('shows different hours for different days', function () {
            // Mock current time to Thursday 11:00 AM (within Thursday hours)
            $this->travelTo(now()->startOfWeek()->addDays(3)->setTime(11, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee($this->menuItem->name);

            // Mock current time to Thursday 7:00 PM (outside Thursday hours)
            $this->travelTo(now()->startOfWeek()->addDays(3)->setTime(19, 0));

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name);
        });
    });

    describe('Always Open Mode', function () {
        beforeEach(function () {
            $this->store->update([
                'settings' => [
                    'always_open' => true,
                    'opening_hours' => []
                ]
            ]);
        });

        it('shows menu items 24/7 when always open', function () {
            // Mock different times
            $this->travelTo(now()->setTime(2, 0)); // 2:00 AM
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee($this->menuItem->name);

            $this->travelTo(now()->setTime(14, 0)); // 2:00 PM
            $this->get('/menu')
                ->assertSee($this->menuItem->name);

            $this->travelTo(now()->setTime(23, 0)); // 11:00 PM
            $this->get('/menu')
                ->assertSee($this->menuItem->name);
        });

        it('allows checkout 24/7 when always open', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            CartItem::factory()->create([
                'user_id' => $customer->id,
                'menu_item_id' => $this->menuItem->id,
                'qty' => 1
            ]);

            // Mock different times
            $this->travelTo(now()->setTime(2, 0)); // 2:00 AM
            $this->post('/checkout', [
                'delivery_address' => '123 Main St, Test City, Test State, 12345',
                'payment_method' => 'card'
            ])->assertStatus(200);

            expect(Order::count())->toBe(1);
        });
    });

    describe('Menu Item Availability', function () {
        it('shows active menu items', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee($this->menuItem->name);
        });

        it('hides inactive menu items', function () {
            $this->menuItem->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name);
        });

        it('prevents adding inactive items to cart', function () {
            $this->menuItem->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->post('/cart/add', [
                'menu_item_id' => $this->menuItem->id,
                'qty' => 1
            ])->assertStatus(403);

            expect(CartItem::count())->toBe(0);
        });

        it('shows out of stock message for unavailable items', function () {
            $this->menuItem->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee('Currently unavailable');
        });
    });

    describe('Store Availability Edge Cases', function () {
        it('handles store with no opening hours set', function () {
            $this->store->update([
                'settings' => [
                    'opening_hours' => [],
                    'always_open' => false
                ]
            ]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name)
                ->assertSee('Store hours not set');
        });

        it('handles invalid time formats gracefully', function () {
            $this->store->update([
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '25:00', 'close' => '17:00'],
                    ],
                    'always_open' => false
                ]
            ]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name)
                ->assertSee('Store hours configuration error');
        });

        it('handles missing settings gracefully', function () {
            $this->store->update(['settings' => null]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name)
                ->assertSee('Store hours not configured');
        });

        it('handles timezone differences', function () {
            // Mock different timezones
            $this->travelTo(now()->setTimezone('America/New_York')->setTime(10, 0));

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee($this->menuItem->name);
        });
    });

    describe('Store Availability Notifications', function () {
        it('sends notification when store becomes inactive', function () {
            \Illuminate\Support\Facades\Notification::fake();

            $this->store->update(['is_active' => false]);

            // Assert notification was sent to store admin
            \Illuminate\Support\Facades\Notification::assertSentTo(
                $this->store->admin,
                \App\Mail\StoreStatusChangedNotification::class
            );
        });

        it('sends notification when store becomes active', function () {
            $this->store->update(['is_active' => false]);
            \Illuminate\Support\Facades\Notification::fake();

            $this->store->update(['is_active' => true]);

            \Illuminate\Support\Facades\Notification::assertSentTo(
                $this->store->admin,
                \App\Mail\StoreStatusChangedNotification::class
            );
        });

        it('notifies customers about store closure', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            // Add item to cart
            CartItem::factory()->create([
                'user_id' => $customer->id,
                'menu_item_id' => $this->menuItem->id,
                'qty' => 1
            ]);

            \Illuminate\Support\Facades\Notification::fake();

            $this->store->update(['is_active' => false]);

            \Illuminate\Support\Facades\Notification::assertSentTo(
                $customer,
                \App\Mail\StoreClosedNotification::class
            );
        });
    });

    describe('Store Availability API', function () {
        it('provides store availability API endpoint', function () {
            $this->get('/api/store/availability')
                ->assertStatus(200)
                ->assertJson([
                    'is_active' => true,
                    'is_open' => true,
                    'next_opening' => null
                ]);
        });

        it('provides store hours API endpoint', function () {
            $this->get('/api/store/hours')
                ->assertStatus(200)
                ->assertJsonStructure([
                    'opening_hours',
                    'always_open',
                    'timezone'
                ]);
        });

        it('provides menu availability API endpoint', function () {
            $this->get('/api/menu/availability')
                ->assertStatus(200)
                ->assertJsonStructure([
                    'items' => [
                        '*' => [
                            'id',
                            'name',
                            'is_available',
                            'reason'
                        ]
                    ]
                ]);
        });
    });
});
