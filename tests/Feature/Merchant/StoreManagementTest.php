<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Livewire;
use App\Livewire\Admin\Store\StoreDetails;
use App\Livewire\Admin\Store\StoreSelector;

describe('Store Management', function () {
    beforeEach(function () {
        $this->merchant = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->merchant);
    });

    describe('Store Creation', function () {
        it('can create a new store', function () {
            $storeData = [
                'name' => 'Test Restaurant',
                'slug' => 'test-restaurant',
                'description' => 'A test restaurant',
                'address_line1' => '123 Main St',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345',
                'phone' => '555-0123',
                'email' => 'test@restaurant.com',
                'is_active' => true,
                'admin_id' => $this->merchant->id,
            ];

            $store = Store::create($storeData);

            expect($store->name)->toBe('Test Restaurant');
            expect($store->slug)->toBe('test-restaurant');
            expect($store->admin_id)->toBe($this->merchant->id);
            expect($store->is_active)->toBeTrue();
        });

        it('generates unique slug for store', function () {
            Store::create([
                'name' => 'First Restaurant',
                'slug' => 'first-restaurant',
                'admin_id' => $this->merchant->id,
            ]);

            $store2 = Store::create([
                'name' => 'First Restaurant',
                'slug' => 'first-restaurant',
                'admin_id' => $this->merchant->id,
            ]);

            expect($store2->slug)->not->toBe('first-restaurant');
            expect($store2->slug)->toContain('first-restaurant');
        });

        it('validates required store fields', function () {
            $this->expectException(\Illuminate\Database\QueryException::class);

            Store::create([
                'name' => '', // Empty name should fail
                'admin_id' => $this->merchant->id,
            ]);
        });
    });

    describe('Store Selector', function () {
        beforeEach(function () {
            $this->store1 = Store::factory()->create(['admin_id' => $this->merchant->id, 'name' => 'Store One']);
            $this->store2 = Store::factory()->create(['admin_id' => $this->merchant->id, 'name' => 'Store Two']);
        });

        it('displays all stores owned by merchant', function () {
            Livewire::test(StoreSelector::class)
                ->assertSee('Store One')
                ->assertSee('Store Two');
        });

        it('can switch between stores', function () {
            Livewire::test(StoreSelector::class)
                ->call('switchStore', $this->store2->id)
                ->assertRedirect();

            expect(session('selected_store_id'))->toBe($this->store2->id);
        });

        it('only shows stores owned by current merchant', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create(['admin_id' => $otherMerchant->id, 'name' => 'Other Store']);

            Livewire::test(StoreSelector::class)
                ->assertSee('Store One')
                ->assertSee('Store Two')
                ->assertDontSee('Other Store');
        });

        it('shows current store as selected', function () {
            session(['selected_store_id' => $this->store1->id]);

            Livewire::test(StoreSelector::class)
                ->assertSee('Store One')
                ->assertSee('Store Two');
        });
    });

    describe('Store Details Management', function () {
        beforeEach(function () {
            $this->store = Store::factory()->create(['admin_id' => $this->merchant->id]);
        });

        it('can update store basic details', function () {
            Livewire::test(StoreDetails::class)
                ->set('name', 'Updated Restaurant Name')
                ->set('description', 'Updated description')
                ->set('phone', '555-9999')
                ->set('email', 'updated@restaurant.com')
                ->call('save')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->name)->toBe('Updated Restaurant Name');
            expect($this->store->description)->toBe('Updated description');
            expect($this->store->phone)->toBe('555-9999');
            expect($this->store->email)->toBe('updated@restaurant.com');
        });

        it('validates store name is required', function () {
            Livewire::test(StoreDetails::class)
                ->set('name', '')
                ->call('save')
                ->assertHasErrors(['name']);
        });

        it('validates email format', function () {
            Livewire::test(StoreDetails::class)
                ->set('email', 'invalid-email')
                ->call('save')
                ->assertHasErrors(['email']);
        });

        it('validates phone format', function () {
            Livewire::test(StoreDetails::class)
                ->set('phone', 'invalid-phone')
                ->call('save')
                ->assertHasErrors(['phone']);
        });

        it('can update store address', function () {
            Livewire::test(StoreDetails::class)
                ->set('address_line1', '456 New Street')
                ->set('address_line2', 'Suite 100')
                ->set('city', 'New City')
                ->set('state', 'New State')
                ->set('postal_code', '54321')
                ->call('save')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->address_line1)->toBe('456 New Street');
            expect($this->store->address_line2)->toBe('Suite 100');
            expect($this->store->city)->toBe('New City');
            expect($this->store->state)->toBe('New State');
            expect($this->store->postal_code)->toBe('54321');
        });

        it('can toggle store active status', function () {
            expect($this->store->is_active)->toBeTrue();

            Livewire::test(StoreDetails::class)
                ->set('is_active', false)
                ->call('save')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->is_active)->toBeFalse();
        });

        it('prevents unauthorized access to other merchants stores', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create(['admin_id' => $otherMerchant->id]);

            $this->actingAs($otherMerchant);

            Livewire::test(StoreDetails::class)
                ->assertDontSee($this->store->name);
        });
    });

    describe('Store Relationships', function () {
        beforeEach(function () {
            $this->store = Store::factory()->create(['admin_id' => $this->merchant->id]);
        });

        it('can have multiple categories', function () {
            $category1 = Category::factory()->create(['store_id' => $this->store->id]);
            $category2 = Category::factory()->create(['store_id' => $this->store->id]);

            expect($this->store->categories)->toHaveCount(2);
            expect($this->store->categories->pluck('id')->toArray())->toContain($category1->id, $category2->id);
        });

        it('can have multiple menu items', function () {
            $menuItem1 = MenuItem::factory()->create(['store_id' => $this->store->id]);
            $menuItem2 = MenuItem::factory()->create(['store_id' => $this->store->id]);

            expect($this->store->menuItems)->toHaveCount(2);
            expect($this->store->menuItems->pluck('id')->toArray())->toContain($menuItem1->id, $menuItem2->id);
        });

        it('belongs to admin user', function () {
            expect($this->store->admin)->toBeInstanceOf(User::class);
            expect($this->store->admin->id)->toBe($this->merchant->id);
        });
    });

    describe('Store Availability', function () {
        beforeEach(function () {
            $this->store = Store::factory()->create([
                'admin_id' => $this->merchant->id,
                'is_active' => true,
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                        ['day' => 'Tuesday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                    ],
                    'always_open' => false
                ]
            ]);
        });

        it('can check if store is currently open', function () {
            // Mock current time to be within opening hours
            $this->travelTo(now()->setTime(10, 0)); // 10:00 AM

            expect($this->store->isCurrentlyOpen())->toBeTrue();
        });

        it('can check if store is currently closed', function () {
            // Mock current time to be outside opening hours
            $this->travelTo(now()->setTime(20, 0)); // 8:00 PM

            expect($this->store->isCurrentlyOpen())->toBeFalse();
        });

        it('returns false when store is inactive', function () {
            $this->store->update(['is_active' => false]);

            expect($this->store->isCurrentlyOpen())->toBeFalse();
        });

        it('can get next opening time', function () {
            // Mock current time to be outside opening hours
            $this->travelTo(now()->setTime(20, 0)); // 8:00 PM

            $nextOpening = $this->store->getNextOpeningTime();
            expect($nextOpening)->toContain('Opens');
        });
    });
});
