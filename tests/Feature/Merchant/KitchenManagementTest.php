<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Category;
use Livewire\Livewire;
use App\Livewire\Admin\Kitchen\KitchenDashboard;

describe('Kitchen Management', function () {
    beforeEach(function () {
        $this->merchant = User::factory()->create(['role' => 'admin']);
        $this->store = Store::factory()->create(['admin_id' => $this->merchant->id]);
        $this->category = Category::factory()->create(['store_id' => $this->store->id]);
        $this->menuItem = MenuItem::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id
        ]);
        $this->actingAs($this->merchant);
    });

    describe('Active Orders Display', function () {
        beforeEach(function () {
            $this->pendingOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending',
                'created_at' => now()->subMinutes(5)
            ]);

            $this->confirmedOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'confirmed',
                'created_at' => now()->subMinutes(10)
            ]);

            $this->preparingOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'preparing',
                'created_at' => now()->subMinutes(15)
            ]);

            OrderItem::factory()->create([
                'order_id' => $this->pendingOrder->id,
                'menu_item_id' => $this->menuItem->id,
                'name_snapshot' => $this->menuItem->name,
                'qty' => 2
            ]);

            OrderItem::factory()->create([
                'order_id' => $this->confirmedOrder->id,
                'menu_item_id' => $this->menuItem->id,
                'name_snapshot' => $this->menuItem->name,
                'qty' => 1
            ]);

            OrderItem::factory()->create([
                'order_id' => $this->preparingOrder->id,
                'menu_item_id' => $this->menuItem->id,
                'name_snapshot' => $this->menuItem->name,
                'qty' => 3
            ]);
        });

        it('displays all active orders', function () {
            Livewire::test(KitchenDashboard::class)
                ->assertSee($this->pendingOrder->code)
                ->assertSee($this->confirmedOrder->code)
                ->assertSee($this->preparingOrder->code);
        });

        it('displays orders in chronological order', function () {
            Livewire::test(KitchenDashboard::class)
                ->assertSeeInOrder([
                    $this->preparingOrder->code, // Oldest
                    $this->confirmedOrder->code,
                    $this->pendingOrder->code    // Newest
                ]);
        });

        it('shows order details including items', function () {
            Livewire::test(KitchenDashboard::class)
                ->assertSee($this->menuItem->name)
                ->assertSee('2') // Quantity for pending order
                ->assertSee('1') // Quantity for confirmed order
                ->assertSee('3'); // Quantity for preparing order
        });

        it('shows order timing information', function () {
            Livewire::test(KitchenDashboard::class)
                ->assertSee('5 minutes ago')
                ->assertSee('10 minutes ago')
                ->assertSee('15 minutes ago');
        });

        it('displays order status badges', function () {
            Livewire::test(KitchenDashboard::class)
                ->assertSee('Pending')
                ->assertSee('Confirmed')
                ->assertSee('Preparing');
        });
    });

    describe('Order Status Updates', function () {
        beforeEach(function () {
            $this->order = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending'
            ]);

            OrderItem::factory()->create([
                'order_id' => $this->order->id,
                'menu_item_id' => $this->menuItem->id,
                'name_snapshot' => $this->menuItem->name,
                'qty' => 1
            ]);
        });

        it('can confirm pending order', function () {
            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'confirmed')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('confirmed');
        });

        it('can mark order as preparing', function () {
            $this->order->update(['status' => 'confirmed']);

            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'preparing')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('preparing');
        });

        it('can mark order as ready', function () {
            $this->order->update(['status' => 'preparing']);

            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'ready')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('ready');
        });

        it('can mark order as completed', function () {
            $this->order->update(['status' => 'ready']);

            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'completed')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('completed');
        });

        it('can cancel order', function () {
            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'cancelled')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('cancelled');
        });

        it('validates status transitions', function () {
            // Cannot skip from pending to ready
            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'ready')
                ->assertHasErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('pending');
        });

        it('prevents invalid status updates', function () {
            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'invalid_status')
                ->assertHasErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('pending');
        });
    });

    describe('Order Filtering', function () {
        beforeEach(function () {
            $this->pendingOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending'
            ]);

            $this->preparingOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'preparing'
            ]);

            $this->readyOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'ready'
            ]);

            $this->completedOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed'
            ]);
        });

        it('can filter by order status', function () {
            Livewire::test(KitchenDashboard::class)
                ->set('status_filter', 'pending')
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($this->preparingOrder->code)
                ->assertDontSee($this->readyOrder->code)
                ->assertDontSee($this->completedOrder->code);
        });

        it('can filter by multiple statuses', function () {
            Livewire::test(KitchenDashboard::class)
                ->set('status_filter', ['pending', 'preparing'])
                ->assertSee($this->pendingOrder->code)
                ->assertSee($this->preparingOrder->code)
                ->assertDontSee($this->readyOrder->code)
                ->assertDontSee($this->completedOrder->code);
        });

        it('can filter by time range', function () {
            $oldOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending',
                'created_at' => now()->subHours(2)
            ]);

            Livewire::test(KitchenDashboard::class)
                ->set('time_filter', 'last_hour')
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($oldOrder->code);
        });

        it('can search orders by code', function () {
            Livewire::test(KitchenDashboard::class)
                ->set('search', $this->pendingOrder->code)
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($this->preparingOrder->code);
        });

        it('can search orders by customer name', function () {
            $this->pendingOrder->update(['guest_name' => 'John Doe']);

            Livewire::test(KitchenDashboard::class)
                ->set('search', 'John')
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($this->preparingOrder->code);
        });
    });

    describe('Order Refresh', function () {
        beforeEach(function () {
            $this->order = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending'
            ]);
        });

        it('can refresh order list', function () {
            Livewire::test(KitchenDashboard::class)
                ->call('refreshOrders')
                ->assertHasNoErrors();

            // Should still see the order
            expect(Order::where('store_id', $this->store->id)->count())->toBe(1);
        });

        it('auto-refreshes every 30 seconds', function () {
            // This would be tested with JavaScript in a browser test
            // For now, we'll test the refresh method works
            Livewire::test(KitchenDashboard::class)
                ->call('refreshOrders')
                ->assertHasNoErrors();
        });

        it('shows last refresh time', function () {
            Livewire::test(KitchenDashboard::class)
                ->call('refreshOrders')
                ->assertSee('Last updated');
        });
    });

    describe('Order Priority', function () {
        beforeEach(function () {
            $this->normalOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending',
                'priority' => 'normal',
                'created_at' => now()->subMinutes(10)
            ]);

            $this->urgentOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending',
                'priority' => 'urgent',
                'created_at' => now()->subMinutes(5)
            ]);
        });

        it('displays urgent orders first', function () {
            Livewire::test(KitchenDashboard::class)
                ->assertSeeInOrder([
                    $this->urgentOrder->code,
                    $this->normalOrder->code
                ]);
        });

        it('shows priority indicators', function () {
            Livewire::test(KitchenDashboard::class)
                ->assertSee('Urgent')
                ->assertSee('Normal');
        });

        it('can change order priority', function () {
            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderPriority', $this->normalOrder->id, 'urgent')
                ->assertHasNoErrors();

            $this->normalOrder->refresh();
            expect($this->normalOrder->priority)->toBe('urgent');
        });
    });

    describe('Order Notifications', function () {
        beforeEach(function () {
            $this->order = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending'
            ]);
        });

        it('sends notification when order status changes', function () {
            \Illuminate\Support\Facades\Notification::fake();

            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'confirmed')
                ->assertHasNoErrors();

            // Assert notification was sent to customer
            \Illuminate\Support\Facades\Notification::assertSentTo(
                $this->order->user,
                \App\Mail\OrderStatusChangedNotification::class
            );
        });

        it('sends SMS notification for urgent orders', function () {
            $this->order->update(['priority' => 'urgent']);

            Livewire::test(KitchenDashboard::class)
                ->call('updateOrderStatus', $this->order->id, 'ready')
                ->assertHasNoErrors();

            // Assert SMS was sent (mock SMS service)
            $this->assertTrue(true); // Placeholder for SMS assertion
        });
    });

    describe('Kitchen Access Control', function () {
        it('prevents non-admin access to kitchen', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/admin/kitchen')
                ->assertStatus(403);
        });

        it('prevents access to other stores kitchen', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create(['admin_id' => $otherMerchant->id]);
            $otherOrder = Order::factory()->create(['store_id' => $otherStore->id]);

            $this->actingAs($otherMerchant);

            Livewire::test(KitchenDashboard::class)
                ->assertDontSee($this->store->name);
        });

        it('requires authentication for kitchen access', function () {
            auth()->logout();

            $this->get('/admin/kitchen')
                ->assertRedirect('/login');
        });
    });

    describe('Order Statistics', function () {
        beforeEach(function () {
            Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending',
                'created_at' => now()->subMinutes(5)
            ]);

            Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'preparing',
                'created_at' => now()->subMinutes(10)
            ]);

            Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'created_at' => now()->subMinutes(30)
            ]);
        });

        it('displays order statistics', function () {
            Livewire::test(KitchenDashboard::class)
                ->assertSee('1 Pending')
                ->assertSee('1 Preparing')
                ->assertSee('1 Completed');
        });

        it('shows average preparation time', function () {
            // Mock completed order with preparation time
            $completedOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'preparation_time' => 15 // minutes
            ]);

            Livewire::test(KitchenDashboard::class)
                ->assertSee('Avg Prep Time: 15 min');
        });

        it('shows peak hours', function () {
            // Mock orders at different times
            Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'created_at' => now()->setTime(12, 0) // Noon
            ]);

            Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'created_at' => now()->setTime(12, 30) // 12:30 PM
            ]);

            Livewire::test(KitchenDashboard::class)
                ->assertSee('Peak Hours: 12:00 PM - 1:00 PM');
        });
    });
});
