<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Category;
use Livewire\Livewire;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;

describe('Customer Authentication', function () {
    beforeEach(function () {
        $this->store = Store::factory()->create(['is_active' => true]);
        $this->category = Category::factory()->create(['store_id' => $this->store->id]);
        $this->menuItem = MenuItem::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'is_active' => true
        ]);
    });

    describe('Customer Login', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create([
                'role' => 'customer',
                'email' => 'customer@example.com',
                'password' => bcrypt('password123')
            ]);
        });

        it('can login with valid credentials', function () {
            Livewire::test(Login::class)
                ->set('email', 'customer@example.com')
                ->set('password', 'password123')
                ->call('login')
                ->assertRedirect('/');

            expect(auth()->check())->toBeTrue();
            expect(auth()->user()->email)->toBe('customer@example.com');
            expect(auth()->user()->role)->toBe('customer');
        });

        it('cannot login with invalid credentials', function () {
            Livewire::test(Login::class)
                ->set('email', 'customer@example.com')
                ->set('password', 'wrongpassword')
                ->call('login')
                ->assertHasErrors(['email']);

            expect(auth()->check())->toBeFalse();
        });

        it('redirects authenticated customer to home page', function () {
            auth()->login($this->customer);

            $this->get('/login')
                ->assertRedirect('/');
        });

        it('can logout successfully', function () {
            auth()->login($this->customer);
            expect(auth()->check())->toBeTrue();

            $this->post('/logout');

            expect(auth()->check())->toBeFalse();
        });
    });

    describe('Customer Registration', function () {
        it('can register new customer account', function () {
            Livewire::test(Register::class)
                ->set('name', 'New Customer')
                ->set('email', 'newcustomer@example.com')
                ->set('password', 'password123')
                ->set('password_confirmation', 'password123')
                ->call('register')
                ->assertRedirect('/');

            expect(User::where('email', 'newcustomer@example.com')->exists())->toBeTrue();

            $user = User::where('email', 'newcustomer@example.com')->first();
            expect($user->role)->toBe('customer');
            expect(auth()->check())->toBeTrue();
        });

        it('validates password confirmation during registration', function () {
            Livewire::test(Register::class)
                ->set('name', 'New Customer')
                ->set('email', 'newcustomer@example.com')
                ->set('password', 'password123')
                ->set('password_confirmation', 'differentpassword')
                ->call('register')
                ->assertHasErrors(['password']);
        });

        it('prevents duplicate email registration', function () {
            User::factory()->create(['email' => 'existing@example.com', 'role' => 'customer']);

            Livewire::test(Register::class)
                ->set('name', 'Duplicate Customer')
                ->set('email', 'existing@example.com')
                ->set('password', 'password123')
                ->set('password_confirmation', 'password123')
                ->call('register')
                ->assertHasErrors(['email']);
        });

        it('validates minimum password length', function () {
            Livewire::test(Register::class)
                ->set('name', 'New Customer')
                ->set('email', 'newcustomer@example.com')
                ->set('password', '123')
                ->set('password_confirmation', '123')
                ->call('register')
                ->assertHasErrors(['password']);
        });

        it('validates required fields', function () {
            Livewire::test(Register::class)
                ->call('register')
                ->assertHasErrors(['name', 'email', 'password']);
        });
    });

    describe('Existing vs Non-existing Customer', function () {
        it('handles login for existing customer', function () {
            $existingCustomer = User::factory()->create([
                'role' => 'customer',
                'email' => 'existing@example.com',
                'password' => bcrypt('password123')
            ]);

            Livewire::test(Login::class)
                ->set('email', 'existing@example.com')
                ->set('password', 'password123')
                ->call('login')
                ->assertRedirect('/');

            expect(auth()->user()->id)->toBe($existingCustomer->id);
        });

        it('handles login attempt for non-existing customer', function () {
            Livewire::test(Login::class)
                ->set('email', 'nonexistent@example.com')
                ->set('password', 'password123')
                ->call('login')
                ->assertHasErrors(['email']);

            expect(auth()->check())->toBeFalse();
        });

        it('allows non-existing customer to register', function () {
            Livewire::test(Register::class)
                ->set('name', 'New Customer')
                ->set('email', 'nonexistent@example.com')
                ->set('password', 'password123')
                ->set('password_confirmation', 'password123')
                ->call('register')
                ->assertRedirect('/');

            expect(User::where('email', 'nonexistent@example.com')->exists())->toBeTrue();
        });
    });

    describe('Customer Order History', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);
        });

        it('can view order history', function () {
            $order = Order::factory()->create([
                'user_id' => $this->customer->id,
                'store_id' => $this->store->id,
                'status' => 'completed'
            ]);

            OrderItem::factory()->create([
                'order_id' => $order->id,
                'menu_item_id' => $this->menuItem->id,
                'name_snapshot' => $this->menuItem->name,
                'unit_price' => $this->menuItem->price,
                'qty' => 2
            ]);

            $this->get('/orders')
                ->assertSee($order->code)
                ->assertSee($this->menuItem->name)
                ->assertSee('$' . number_format($this->menuItem->price * 2, 2));
        });

        it('can view individual order details', function () {
            $order = Order::factory()->create([
                'user_id' => $this->customer->id,
                'store_id' => $this->store->id,
                'status' => 'completed'
            ]);

            $this->get("/order/{$order->code}")
                ->assertSee($order->code)
                ->assertSee($this->store->name);
        });

        it('cannot view other customers orders', function () {
            $otherCustomer = User::factory()->create(['role' => 'customer']);
            $otherOrder = Order::factory()->create([
                'user_id' => $otherCustomer->id,
                'store_id' => $this->store->id
            ]);

            $this->get("/order/{$otherOrder->code}")
                ->assertStatus(403);
        });

        it('shows order status correctly', function () {
            $order = Order::factory()->create([
                'user_id' => $this->customer->id,
                'store_id' => $this->store->id,
                'status' => 'preparing'
            ]);

            $this->get("/order/{$order->code}")
                ->assertSee('Preparing');
        });
    });

    describe('Customer Profile Management', function () {
        beforeEach(function () {
            $this->customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($this->customer);
        });

        it('can update profile information', function () {
            $this->patch('/profile', [
                'name' => 'Updated Name',
                'email' => 'updated@example.com'
            ])->assertRedirect('/profile');

            $this->customer->refresh();
            expect($this->customer->name)->toBe('Updated Name');
            expect($this->customer->email)->toBe('updated@example.com');
        });

        it('can add delivery address', function () {
            $this->post('/addresses', [
                'address_line1' => '123 Main St',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'is_default' => true
            ])->assertRedirect('/profile');

            expect($this->customer->addresses()->count())->toBe(1);
            expect($this->customer->addresses()->first()->is_default)->toBeTrue();
        });

        it('can set default address', function () {
            $address1 = $this->customer->addresses()->create([
                'address_line1' => '123 Main St',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'is_default' => false
            ]);

            $address2 = $this->customer->addresses()->create([
                'address_line1' => '456 Oak St',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'is_default' => false
            ]);

            $this->patch("/addresses/{$address2->id}/default")
                ->assertRedirect('/profile');

            $address1->refresh();
            $address2->refresh();

            expect($address1->is_default)->toBeFalse();
            expect($address2->is_default)->toBeTrue();
        });
    });

    describe('Customer Access Control', function () {
        it('prevents customer from accessing admin areas', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/admin')
                ->assertStatus(403);

            $this->get('/admin/menu')
                ->assertStatus(403);
        });

        it('allows customer to access customer areas', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertStatus(200);

            $this->get('/cart')
                ->assertStatus(200);

            $this->get('/orders')
                ->assertStatus(200);
        });

        it('redirects unauthenticated user to login for protected routes', function () {
            $this->get('/orders')
                ->assertRedirect('/login');

            $this->get('/cart')
                ->assertRedirect('/login');
        });
    });
});
