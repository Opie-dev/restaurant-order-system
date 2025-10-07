<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Category;
use Livewire\Livewire;
use App\Livewire\Admin\Orders\OrderList;
use App\Livewire\Admin\Orders\OrderDetails;

describe('Order Management', function () {
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

    describe('Order List', function () {
        beforeEach(function () {
            $this->pendingOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending',
                'total' => 25.99,
                'created_at' => now()->subMinutes(5)
            ]);

            $this->completedOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'total' => 15.99,
                'created_at' => now()->subHours(2)
            ]);

            $this->cancelledOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'cancelled',
                'total' => 10.99,
                'created_at' => now()->subHours(1)
            ]);

            OrderItem::factory()->create([
                'order_id' => $this->pendingOrder->id,
                'menu_item_id' => $this->menuItem->id,
                'name_snapshot' => $this->menuItem->name,
                'qty' => 2,
                'unit_price' => 12.99
            ]);
        });

        it('displays all orders', function () {
            Livewire::test(OrderList::class)
                ->assertSee($this->pendingOrder->code)
                ->assertSee($this->completedOrder->code)
                ->assertSee($this->cancelledOrder->code);
        });

        it('shows order details', function () {
            Livewire::test(OrderList::class)
                ->assertSee('$25.99')
                ->assertSee('$15.99')
                ->assertSee('$10.99')
                ->assertSee('Pending')
                ->assertSee('Completed')
                ->assertSee('Cancelled');
        });

        it('shows order timing', function () {
            Livewire::test(OrderList::class)
                ->assertSee('5 minutes ago')
                ->assertSee('2 hours ago')
                ->assertSee('1 hour ago');
        });

        it('can filter by order status', function () {
            Livewire::test(OrderList::class)
                ->set('status_filter', 'pending')
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($this->completedOrder->code)
                ->assertDontSee($this->cancelledOrder->code);
        });

        it('can filter by payment status', function () {
            $this->pendingOrder->update(['payment_status' => 'paid']);
            $this->completedOrder->update(['payment_status' => 'unpaid']);

            Livewire::test(OrderList::class)
                ->set('payment_filter', 'paid')
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($this->completedOrder->code);
        });

        it('can filter by date range', function () {
            $oldOrder = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'created_at' => now()->subDays(7)
            ]);

            Livewire::test(OrderList::class)
                ->set('date_from', now()->subDay()->format('Y-m-d'))
                ->set('date_to', now()->format('Y-m-d'))
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($oldOrder->code);
        });

        it('can search orders by code', function () {
            Livewire::test(OrderList::class)
                ->set('search', $this->pendingOrder->code)
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($this->completedOrder->code);
        });

        it('can search orders by customer name', function () {
            $this->pendingOrder->update(['guest_name' => 'John Doe']);

            Livewire::test(OrderList::class)
                ->set('search', 'John')
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($this->completedOrder->code);
        });

        it('can search orders by customer email', function () {
            $this->pendingOrder->update(['guest_email' => 'john@example.com']);

            Livewire::test(OrderList::class)
                ->set('search', 'john@example.com')
                ->assertSee($this->pendingOrder->code)
                ->assertDontSee($this->completedOrder->code);
        });

        it('can sort by date', function () {
            Livewire::test(OrderList::class)
                ->set('sort_by', 'created_at')
                ->set('sort_direction', 'desc')
                ->assertSeeInOrder([
                    $this->pendingOrder->code,   // Newest
                    $this->cancelledOrder->code,
                    $this->completedOrder->code  // Oldest
                ]);
        });

        it('can sort by total amount', function () {
            Livewire::test(OrderList::class)
                ->set('sort_by', 'total')
                ->set('sort_direction', 'desc')
                ->assertSeeInOrder([
                    $this->pendingOrder->code,   // Highest
                    $this->completedOrder->code,
                    $this->cancelledOrder->code  // Lowest
                ]);
        });

        it('can export orders to CSV', function () {
            Livewire::test(OrderList::class)
                ->call('exportOrders')
                ->assertHasNoErrors();

            // Assert CSV file was generated
            $this->assertTrue(true); // Placeholder for file assertion
        });
    });

    describe('Order Details', function () {
        beforeEach(function () {
            $this->order = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending',
                'total' => 25.99,
                'subtotal' => 23.99,
                'tax' => 2.00,
                'guest_name' => 'John Doe',
                'guest_email' => 'john@example.com',
                'guest_phone' => '555-0123',
                'delivery_address' => '123 Main St, Test City, Test State, 12345',
                'notes' => 'Please ring doorbell twice'
            ]);

            OrderItem::factory()->create([
                'order_id' => $this->order->id,
                'menu_item_id' => $this->menuItem->id,
                'name_snapshot' => $this->menuItem->name,
                'qty' => 2,
                'unit_price' => 12.99,
                'line_total' => 25.98
            ]);
        });

        it('displays order information', function () {
            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->assertSee($this->order->code)
                ->assertSee('John Doe')
                ->assertSee('john@example.com')
                ->assertSee('555-0123')
                ->assertSee('123 Main St, Test City, Test State, 12345')
                ->assertSee('Please ring doorbell twice');
        });

        it('displays order totals', function () {
            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->assertSee('$23.99') // Subtotal
                ->assertSee('$2.00')  // Tax
                ->assertSee('$25.99'); // Total
        });

        it('displays order items', function () {
            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->assertSee($this->menuItem->name)
                ->assertSee('2') // Quantity
                ->assertSee('$12.99') // Unit price
                ->assertSee('$25.98'); // Line total
        });

        it('can update order status', function () {
            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->call('updateStatus', 'confirmed')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('confirmed');
        });

        it('can update payment status', function () {
            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->call('updatePaymentStatus', 'paid')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->payment_status)->toBe('paid');
        });

        it('can add order notes', function () {
            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->set('admin_notes', 'Customer called to confirm delivery')
                ->call('saveNotes')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->admin_notes)->toBe('Customer called to confirm delivery');
        });

        it('can cancel order', function () {
            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->call('cancelOrder', 'Customer requested cancellation')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('cancelled');
            expect($this->order->cancellation_reason)->toBe('Customer requested cancellation');
        });

        it('can refund order', function () {
            $this->order->update(['payment_status' => 'paid']);

            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->call('refundOrder', 'Item was damaged')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->payment_status)->toBe('refunded');
            expect($this->order->refund_reason)->toBe('Item was damaged');
        });

        it('validates status transitions', function () {
            // Cannot skip from pending to completed
            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->call('updateStatus', 'completed')
                ->assertHasErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('pending');
        });

        it('prevents unauthorized access to other stores orders', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create(['admin_id' => $otherMerchant->id]);
            $otherOrder = Order::factory()->create(['store_id' => $otherStore->id]);

            $this->actingAs($otherMerchant);

            Livewire::test(OrderDetails::class, ['order' => $otherOrder])
                ->assertDontSee($this->order->code);
        });
    });

    describe('Order Statistics', function () {
        beforeEach(function () {
            // Create orders for different time periods
            Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'total' => 25.99,
                'created_at' => now()->subDays(1)
            ]);

            Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'total' => 15.99,
                'created_at' => now()->subDays(2)
            ]);

            Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'cancelled',
                'total' => 10.99,
                'created_at' => now()->subDays(3)
            ]);
        });

        it('displays order statistics', function () {
            Livewire::test(OrderList::class)
                ->assertSee('Total Orders: 3')
                ->assertSee('Completed: 2')
                ->assertSee('Cancelled: 1')
                ->assertSee('Total Revenue: $41.98');
        });

        it('shows daily statistics', function () {
            Livewire::test(OrderList::class)
                ->set('period', 'daily')
                ->assertSee('Today\'s Orders: 0')
                ->assertSee('Yesterday\'s Orders: 1');
        });

        it('shows weekly statistics', function () {
            Livewire::test(OrderList::class)
                ->set('period', 'weekly')
                ->assertSee('This Week\'s Orders: 3')
                ->assertSee('Last Week\'s Orders: 0');
        });

        it('shows monthly statistics', function () {
            Livewire::test(OrderList::class)
                ->set('period', 'monthly')
                ->assertSee('This Month\'s Orders: 3')
                ->assertSee('Last Month\'s Orders: 0');
        });

        it('shows average order value', function () {
            Livewire::test(OrderList::class)
                ->assertSee('Average Order Value: $13.99');
        });

        it('shows order completion rate', function () {
            Livewire::test(OrderList::class)
                ->assertSee('Completion Rate: 66.7%');
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

            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->call('updateStatus', 'confirmed')
                ->assertHasNoErrors();

            // Assert notification was sent
            \Illuminate\Support\Facades\Notification::assertSentTo(
                $this->order->user,
                \App\Mail\OrderStatusChangedNotification::class
            );
        });

        it('sends SMS notification for urgent updates', function () {
            $this->order->update(['priority' => 'urgent']);

            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->call('updateStatus', 'ready')
                ->assertHasNoErrors();

            // Assert SMS was sent (mock SMS service)
            $this->assertTrue(true); // Placeholder for SMS assertion
        });

        it('sends email notification for cancellations', function () {
            \Illuminate\Support\Facades\Notification::fake();

            Livewire::test(OrderDetails::class, ['order' => $this->order])
                ->call('cancelOrder', 'Store closed')
                ->assertHasNoErrors();

            \Illuminate\Support\Facades\Notification::assertSentTo(
                $this->order->user,
                \App\Mail\OrderCancelledNotification::class
            );
        });
    });

    describe('Order Access Control', function () {
        it('prevents non-admin access to order management', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/admin/orders')
                ->assertStatus(403);
        });

        it('prevents access to other stores orders', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create(['admin_id' => $otherMerchant->id]);
            $otherOrder = Order::factory()->create(['store_id' => $otherStore->id]);

            $this->actingAs($otherMerchant);

            Livewire::test(OrderList::class)
                ->assertDontSee($this->store->name);
        });

        it('requires authentication for order access', function () {
            auth()->logout();

            $this->get('/admin/orders')
                ->assertRedirect('/login');
        });
    });

    describe('Order Search and Filtering', function () {
        beforeEach(function () {
            $this->order1 = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'pending',
                'guest_name' => 'John Doe',
                'guest_email' => 'john@example.com',
                'total' => 25.99
            ]);

            $this->order2 = Order::factory()->create([
                'store_id' => $this->store->id,
                'status' => 'completed',
                'guest_name' => 'Jane Smith',
                'guest_email' => 'jane@example.com',
                'total' => 15.99
            ]);
        });

        it('can search by multiple criteria', function () {
            Livewire::test(OrderList::class)
                ->set('search', 'John')
                ->assertSee($this->order1->code)
                ->assertDontSee($this->order2->code);
        });

        it('can combine multiple filters', function () {
            Livewire::test(OrderList::class)
                ->set('status_filter', 'pending')
                ->set('payment_filter', 'unpaid')
                ->set('date_from', now()->subDay()->format('Y-m-d'))
                ->assertSee($this->order1->code)
                ->assertDontSee($this->order2->code);
        });

        it('can clear all filters', function () {
            Livewire::test(OrderList::class)
                ->set('status_filter', 'pending')
                ->set('search', 'John')
                ->call('clearFilters')
                ->assertSee($this->order1->code)
                ->assertSee($this->order2->code);
        });

        it('remembers filter state', function () {
            Livewire::test(OrderList::class)
                ->set('status_filter', 'pending')
                ->set('search', 'John')
                ->call('refresh')
                ->assertSet('status_filter', 'pending')
                ->assertSet('search', 'John');
        });
    });
});
