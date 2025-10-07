<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\UserAddress;
use Livewire\Livewire;
use App\Livewire\Admin\Customers\CustomerList;
use App\Livewire\Admin\Customers\CustomerDetails;

describe('Customer Management', function () {
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

    describe('Customer List', function () {
        beforeEach(function () {
            $this->customer1 = User::factory()->create([
                'role' => 'customer',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'created_at' => now()->subDays(5)
            ]);

            $this->customer2 = User::factory()->create([
                'role' => 'customer',
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'created_at' => now()->subDays(10)
            ]);

            $this->customer3 = User::factory()->create([
                'role' => 'customer',
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'is_disabled' => true,
                'created_at' => now()->subDays(15)
            ]);
        });

        it('displays all customers', function () {
            Livewire::test(CustomerList::class)
                ->assertSee('John Doe')
                ->assertSee('Jane Smith')
                ->assertSee('Bob Johnson');
        });

        it('shows customer status', function () {
            Livewire::test(CustomerList::class)
                ->assertSee('Active')
                ->assertSee('Disabled');
        });

        it('shows customer registration date', function () {
            Livewire::test(CustomerList::class)
                ->assertSee('5 days ago')
                ->assertSee('10 days ago')
                ->assertSee('15 days ago');
        });

        it('shows order count for each customer', function () {
            Order::factory()->create([
                'user_id' => $this->customer1->id,
                'store_id' => $this->store->id,
                'status' => 'completed'
            ]);

            Order::factory()->create([
                'user_id' => $this->customer1->id,
                'store_id' => $this->store->id,
                'status' => 'completed'
            ]);

            Order::factory()->create([
                'user_id' => $this->customer2->id,
                'store_id' => $this->store->id,
                'status' => 'completed'
            ]);

            Livewire::test(CustomerList::class)
                ->assertSee('2 orders')
                ->assertSee('1 order')
                ->assertSee('0 orders');
        });

        it('shows total spent for each customer', function () {
            Order::factory()->create([
                'user_id' => $this->customer1->id,
                'store_id' => $this->store->id,
                'total' => 25.99,
                'status' => 'completed'
            ]);

            Order::factory()->create([
                'user_id' => $this->customer1->id,
                'store_id' => $this->store->id,
                'total' => 15.99,
                'status' => 'completed'
            ]);

            Livewire::test(CustomerList::class)
                ->assertSee('$41.98')
                ->assertSee('$0.00');
        });

        it('can search customers by name', function () {
            Livewire::test(CustomerList::class)
                ->set('search', 'John')
                ->assertSee('John Doe')
                ->assertDontSee('Jane Smith')
                ->assertDontSee('Bob Johnson');
        });

        it('can search customers by email', function () {
            Livewire::test(CustomerList::class)
                ->set('search', 'jane@example.com')
                ->assertSee('Jane Smith')
                ->assertDontSee('John Doe')
                ->assertDontSee('Bob Johnson');
        });

        it('can filter by customer status', function () {
            Livewire::test(CustomerList::class)
                ->set('status_filter', 'active')
                ->assertSee('John Doe')
                ->assertSee('Jane Smith')
                ->assertDontSee('Bob Johnson');
        });

        it('can filter by disabled customers', function () {
            Livewire::test(CustomerList::class)
                ->set('status_filter', 'disabled')
                ->assertDontSee('John Doe')
                ->assertDontSee('Jane Smith')
                ->assertSee('Bob Johnson');
        });

        it('can filter by registration date', function () {
            Livewire::test(CustomerList::class)
                ->set('date_from', now()->subDays(7)->format('Y-m-d'))
                ->assertSee('John Doe')
                ->assertDontSee('Jane Smith')
                ->assertDontSee('Bob Johnson');
        });

        it('can sort by name', function () {
            Livewire::test(CustomerList::class)
                ->set('sort_by', 'name')
                ->set('sort_direction', 'asc')
                ->assertSeeInOrder(['Bob Johnson', 'Jane Smith', 'John Doe']);
        });

        it('can sort by registration date', function () {
            Livewire::test(CustomerList::class)
                ->set('sort_by', 'created_at')
                ->set('sort_direction', 'desc')
                ->assertSeeInOrder(['John Doe', 'Jane Smith', 'Bob Johnson']);
        });

        it('can sort by total spent', function () {
            Order::factory()->create([
                'user_id' => $this->customer2->id,
                'store_id' => $this->store->id,
                'total' => 50.00,
                'status' => 'completed'
            ]);

            Order::factory()->create([
                'user_id' => $this->customer1->id,
                'store_id' => $this->store->id,
                'total' => 25.00,
                'status' => 'completed'
            ]);

            Livewire::test(CustomerList::class)
                ->set('sort_by', 'total_spent')
                ->set('sort_direction', 'desc')
                ->assertSeeInOrder(['Jane Smith', 'John Doe', 'Bob Johnson']);
        });

        it('can disable customer', function () {
            Livewire::test(CustomerList::class)
                ->call('disableCustomer', $this->customer1->id)
                ->assertHasNoErrors();

            $this->customer1->refresh();
            expect($this->customer1->is_disabled)->toBeTrue();
        });

        it('can enable customer', function () {
            Livewire::test(CustomerList::class)
                ->call('enableCustomer', $this->customer3->id)
                ->assertHasNoErrors();

            $this->customer3->refresh();
            expect($this->customer3->is_disabled)->toBeFalse();
        });

        it('can delete customer', function () {
            Livewire::test(CustomerList::class)
                ->call('deleteCustomer', $this->customer3->id)
                ->assertHasNoErrors();

            expect(User::find($this->customer3->id))->toBeNull();
        });

        it('prevents deleting customer with orders', function () {
            Order::factory()->create([
                'user_id' => $this->customer1->id,
                'store_id' => $this->store->id
            ]);

            Livewire::test(CustomerList::class)
                ->call('deleteCustomer', $this->customer1->id)
                ->assertHasErrors();

            expect(User::find($this->customer1->id))->not->toBeNull();
        });
    });

    describe('Customer Details', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create([
                'role' => 'customer',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '555-0123'
            ]);

            $this->order1 = Order::factory()->create([
                'user_id' => $this->customer->id,
                'store_id' => $this->store->id,
                'status' => 'completed',
                'total' => 25.99,
                'created_at' => now()->subDays(5)
            ]);

            $this->order2 = Order::factory()->create([
                'user_id' => $this->customer->id,
                'store_id' => $this->store->id,
                'status' => 'pending',
                'total' => 15.99,
                'created_at' => now()->subDays(2)
            ]);

            OrderItem::factory()->create([
                'order_id' => $this->order1->id,
                'menu_item_id' => $this->menuItem->id,
                'name_snapshot' => $this->menuItem->name,
                'qty' => 2,
                'unit_price' => 12.99
            ]);
        });

        it('displays customer information', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->assertSee('John Doe')
                ->assertSee('john@example.com')
                ->assertSee('555-0123');
        });

        it('displays customer order history', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->assertSee($this->order1->code)
                ->assertSee($this->order2->code)
                ->assertSee('Completed')
                ->assertSee('Pending');
        });

        it('displays order details', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->assertSee($this->menuItem->name)
                ->assertSee('2') // Quantity
                ->assertSee('$12.99') // Unit price
                ->assertSee('$25.99'); // Order total
        });

        it('shows customer statistics', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->assertSee('Total Orders: 2')
                ->assertSee('Total Spent: $41.98')
                ->assertSee('Average Order: $20.99')
                ->assertSee('Last Order: 2 days ago');
        });

        it('can view individual order details', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->call('viewOrder', $this->order1->id)
                ->assertRedirect("/admin/orders/{$this->order1->id}");
        });

        it('can add customer notes', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->set('admin_notes', 'VIP customer, prefers contactless delivery')
                ->call('saveNotes')
                ->assertHasNoErrors();

            $this->customer->refresh();
            expect($this->customer->admin_notes)->toBe('VIP customer, prefers contactless delivery');
        });

        it('can update customer information', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->set('name', 'John Updated')
                ->set('email', 'john.updated@example.com')
                ->set('phone', '555-9999')
                ->call('updateCustomer')
                ->assertHasNoErrors();

            $this->customer->refresh();
            expect($this->customer->name)->toBe('John Updated');
            expect($this->customer->email)->toBe('john.updated@example.com');
            expect($this->customer->phone)->toBe('555-9999');
        });

        it('validates customer information updates', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->set('email', 'invalid-email')
                ->call('updateCustomer')
                ->assertHasErrors(['email']);
        });

        it('can disable customer account', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->call('disableAccount', 'Customer requested account closure')
                ->assertHasNoErrors();

            $this->customer->refresh();
            expect($this->customer->is_disabled)->toBeTrue();
            expect($this->customer->disable_reason)->toBe('Customer requested account closure');
        });

        it('can enable customer account', function () {
            $this->customer->update(['is_disabled' => true]);

            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->call('enableAccount')
                ->assertHasNoErrors();

            $this->customer->refresh();
            expect($this->customer->is_disabled)->toBeFalse();
        });
    });

    describe('Customer Address Management', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
        });

        it('displays customer addresses', function () {
            $address1 = UserAddress::factory()->create([
                'user_id' => $this->customer->id,
                'address_line1' => '123 Main St',
                'city' => 'Test City',
                'is_default' => true
            ]);

            $address2 = UserAddress::factory()->create([
                'user_id' => $this->customer->id,
                'address_line1' => '456 Oak St',
                'city' => 'Test City',
                'is_default' => false
            ]);

            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->assertSee('123 Main St')
                ->assertSee('456 Oak St')
                ->assertSee('Default');
        });

        it('can add new address for customer', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->set('new_address.address_line1', '789 Pine St')
                ->set('new_address.city', 'New City')
                ->set('new_address.state', 'New State')
                ->set('new_address.postal_code', '54321')
                ->call('addAddress')
                ->assertHasNoErrors();

            expect($this->customer->addresses()->count())->toBe(1);
            $address = $this->customer->addresses()->first();
            expect($address->address_line1)->toBe('789 Pine St');
        });

        it('can set default address', function () {
            $address1 = UserAddress::factory()->create([
                'user_id' => $this->customer->id,
                'is_default' => true
            ]);

            $address2 = UserAddress::factory()->create([
                'user_id' => $this->customer->id,
                'is_default' => false
            ]);

            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->call('setDefaultAddress', $address2->id)
                ->assertHasNoErrors();

            $address1->refresh();
            $address2->refresh();

            expect($address1->is_default)->toBeFalse();
            expect($address2->is_default)->toBeTrue();
        });

        it('can delete customer address', function () {
            $address = UserAddress::factory()->create([
                'user_id' => $this->customer->id
            ]);

            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->call('deleteAddress', $address->id)
                ->assertHasNoErrors();

            expect(UserAddress::find($address->id))->toBeNull();
        });
    });

    describe('Customer Order Management', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->order = Order::factory()->create([
                'user_id' => $this->customer->id,
                'store_id' => $this->store->id,
                'status' => 'pending'
            ]);
        });

        it('can view customer orders', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->assertSee($this->order->code)
                ->assertSee('Pending');
        });

        it('can filter customer orders by status', function () {
            $completedOrder = Order::factory()->create([
                'user_id' => $this->customer->id,
                'store_id' => $this->store->id,
                'status' => 'completed'
            ]);

            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->set('order_status_filter', 'pending')
                ->assertSee($this->order->code)
                ->assertDontSee($completedOrder->code);
        });

        it('can filter customer orders by date', function () {
            $oldOrder = Order::factory()->create([
                'user_id' => $this->customer->id,
                'store_id' => $this->store->id,
                'status' => 'completed',
                'created_at' => now()->subDays(30)
            ]);

            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->set('order_date_from', now()->subDays(7)->format('Y-m-d'))
                ->assertSee($this->order->code)
                ->assertDontSee($oldOrder->code);
        });

        it('can cancel customer order', function () {
            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->call('cancelOrder', $this->order->id, 'Customer requested cancellation')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->status)->toBe('cancelled');
            expect($this->order->cancellation_reason)->toBe('Customer requested cancellation');
        });

        it('can refund customer order', function () {
            $this->order->update(['payment_status' => 'paid']);

            Livewire::test(CustomerDetails::class, ['customer' => $this->customer])
                ->call('refundOrder', $this->order->id, 'Customer complaint')
                ->assertHasNoErrors();

            $this->order->refresh();
            expect($this->order->payment_status)->toBe('refunded');
            expect($this->order->refund_reason)->toBe('Customer complaint');
        });
    });

    describe('Customer Access Control', function () {
        it('prevents non-admin access to customer management', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/admin/customers')
                ->assertStatus(403);
        });

        it('prevents access to other stores customers', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create(['admin_id' => $otherMerchant->id]);
            $otherCustomer = User::factory()->create(['role' => 'customer']);

            $this->actingAs($otherMerchant);

            Livewire::test(CustomerList::class)
                ->assertDontSee($this->store->name);
        });

        it('requires authentication for customer access', function () {
            auth()->logout();

            $this->get('/admin/customers')
                ->assertRedirect('/login');
        });
    });

    describe('Customer Statistics', function () {
        beforeEach(function () {
            $this->customer1 = User::factory()->create(['role' => 'customer']);
            $this->customer2 = User::factory()->create(['role' => 'customer']);
            $this->customer3 = User::factory()->create(['role' => 'customer', 'is_disabled' => true]);

            Order::factory()->create([
                'user_id' => $this->customer1->id,
                'store_id' => $this->store->id,
                'total' => 25.99,
                'status' => 'completed'
            ]);

            Order::factory()->create([
                'user_id' => $this->customer2->id,
                'store_id' => $this->store->id,
                'total' => 15.99,
                'status' => 'completed'
            ]);
        });

        it('displays customer statistics', function () {
            Livewire::test(CustomerList::class)
                ->assertSee('Total Customers: 3')
                ->assertSee('Active Customers: 2')
                ->assertSee('Disabled Customers: 1')
                ->assertSee('Total Revenue: $41.98');
        });

        it('shows customer growth over time', function () {
            Livewire::test(CustomerList::class)
                ->set('period', 'monthly')
                ->assertSee('This Month: 3')
                ->assertSee('Last Month: 0');
        });

        it('shows average customer value', function () {
            Livewire::test(CustomerList::class)
                ->assertSee('Average Customer Value: $20.99');
        });

        it('shows customer retention rate', function () {
            Livewire::test(CustomerList::class)
                ->assertSee('Retention Rate: 100%');
        });
    });
});
