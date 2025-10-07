<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Livewire;
use App\Livewire\Admin\Menu\MenuList;
use App\Livewire\Admin\Menu\MenuCreate;
use App\Livewire\Admin\Menu\MenuEdit;

describe('Menu Management', function () {
    beforeEach(function () {
        $this->merchant = User::factory()->create(['role' => 'admin']);
        $this->store = Store::factory()->create(['admin_id' => $this->merchant->id]);
        $this->category = Category::factory()->create(['store_id' => $this->store->id]);
        $this->actingAs($this->merchant);
    });

    describe('Menu List', function () {
        beforeEach(function () {
            $this->menuItem1 = MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id,
                'name' => 'Pizza Margherita',
                'price' => 12.99
            ]);

            $this->menuItem2 = MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id,
                'name' => 'Burger Deluxe',
                'price' => 15.99
            ]);
        });

        it('displays all menu items', function () {
            Livewire::test(MenuList::class)
                ->assertSee('Pizza Margherita')
                ->assertSee('Burger Deluxe')
                ->assertSee('$12.99')
                ->assertSee('$15.99');
        });

        it('can search menu items by name', function () {
            Livewire::test(MenuList::class)
                ->set('search', 'Pizza')
                ->assertSee('Pizza Margherita')
                ->assertDontSee('Burger Deluxe');
        });

        it('can search menu items by description', function () {
            $this->menuItem1->update(['description' => 'Classic Italian pizza with tomato and mozzarella']);

            Livewire::test(MenuList::class)
                ->set('search', 'Italian')
                ->assertSee('Pizza Margherita')
                ->assertDontSee('Burger Deluxe');
        });

        it('can filter by category', function () {
            $category2 = Category::factory()->create(['store_id' => $this->store->id]);
            $this->menuItem2->update(['category_id' => $category2->id]);

            Livewire::test(MenuList::class)
                ->set('category_filter', $this->category->id)
                ->assertSee('Pizza Margherita')
                ->assertDontSee('Burger Deluxe');
        });

        it('can filter by active status', function () {
            $this->menuItem2->update(['is_active' => false]);

            Livewire::test(MenuList::class)
                ->set('status_filter', 'active')
                ->assertSee('Pizza Margherita')
                ->assertDontSee('Burger Deluxe');
        });

        it('can sort by name', function () {
            Livewire::test(MenuList::class)
                ->set('sort_by', 'name')
                ->set('sort_direction', 'asc')
                ->assertSeeInOrder(['Burger Deluxe', 'Pizza Margherita']);
        });

        it('can sort by price', function () {
            Livewire::test(MenuList::class)
                ->set('sort_by', 'price')
                ->set('sort_direction', 'desc')
                ->assertSeeInOrder(['Burger Deluxe', 'Pizza Margherita']);
        });

        it('can toggle menu item active status', function () {
            Livewire::test(MenuList::class)
                ->call('toggleActive', $this->menuItem1->id)
                ->assertHasNoErrors();

            $this->menuItem1->refresh();
            expect($this->menuItem1->is_active)->toBeFalse();
        });

        it('can delete menu item', function () {
            Livewire::test(MenuList::class)
                ->call('deleteMenuItem', $this->menuItem1->id)
                ->assertHasNoErrors();

            expect(MenuItem::find($this->menuItem1->id))->toBeNull();
        });
    });

    describe('Menu Item Creation', function () {
        it('can create a new menu item', function () {
            Livewire::test(MenuCreate::class)
                ->set('name', 'New Pizza')
                ->set('description', 'A delicious new pizza')
                ->set('price', 14.99)
                ->set('category_id', $this->category->id)
                ->set('is_active', true)
                ->call('save')
                ->assertHasNoErrors()
                ->assertRedirect();

            $menuItem = MenuItem::where('name', 'New Pizza')->first();
            expect($menuItem)->not->toBeNull();
            expect($menuItem->price)->toBe(14.99);
            expect($menuItem->store_id)->toBe($this->store->id);
        });

        it('validates required fields', function () {
            Livewire::test(MenuCreate::class)
                ->call('save')
                ->assertHasErrors(['name', 'price', 'category_id']);
        });

        it('validates price is numeric and positive', function () {
            Livewire::test(MenuCreate::class)
                ->set('name', 'Test Item')
                ->set('price', -5.99)
                ->set('category_id', $this->category->id)
                ->call('save')
                ->assertHasErrors(['price']);

            Livewire::test(MenuCreate::class)
                ->set('name', 'Test Item')
                ->set('price', 'invalid')
                ->set('category_id', $this->category->id)
                ->call('save')
                ->assertHasErrors(['price']);
        });

        it('validates category belongs to store', function () {
            $otherStore = Store::factory()->create();
            $otherCategory = Category::factory()->create(['store_id' => $otherStore->id]);

            Livewire::test(MenuCreate::class)
                ->set('name', 'Test Item')
                ->set('price', 10.99)
                ->set('category_id', $otherCategory->id)
                ->call('save')
                ->assertHasErrors(['category_id']);
        });

        it('can upload menu item image', function () {
            $file = \Illuminate\Http\UploadedFile::fake()->image('pizza.jpg', 400, 300);

            Livewire::test(MenuCreate::class)
                ->set('name', 'Pizza with Image')
                ->set('price', 12.99)
                ->set('category_id', $this->category->id)
                ->set('image', $file)
                ->call('save')
                ->assertHasNoErrors();

            $menuItem = MenuItem::where('name', 'Pizza with Image')->first();
            expect($menuItem->image_path)->not->toBeNull();
        });

        it('can create menu item with options', function () {
            $options = [
                ['name' => 'Size', 'required' => true, 'choices' => [
                    ['name' => 'Small', 'price' => 0],
                    ['name' => 'Large', 'price' => 3.00]
                ]],
                ['name' => 'Extra Toppings', 'required' => false, 'choices' => [
                    ['name' => 'Extra Cheese', 'price' => 2.00],
                    ['name' => 'Pepperoni', 'price' => 1.50]
                ]]
            ];

            Livewire::test(MenuCreate::class)
                ->set('name', 'Customizable Pizza')
                ->set('price', 10.99)
                ->set('category_id', $this->category->id)
                ->set('options', $options)
                ->call('save')
                ->assertHasNoErrors();

            $menuItem = MenuItem::where('name', 'Customizable Pizza')->first();
            expect($menuItem->options)->toBe($options);
        });
    });

    describe('Menu Item Editing', function () {
        beforeEach(function () {
            $this->menuItem = MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id,
                'name' => 'Original Pizza',
                'price' => 12.99
            ]);
        });

        it('can update menu item details', function () {
            Livewire::test(MenuEdit::class, ['menuItem' => $this->menuItem])
                ->set('name', 'Updated Pizza')
                ->set('description', 'Updated description')
                ->set('price', 15.99)
                ->call('save')
                ->assertHasNoErrors();

            $this->menuItem->refresh();
            expect($this->menuItem->name)->toBe('Updated Pizza');
            expect($this->menuItem->description)->toBe('Updated description');
            expect($this->menuItem->price)->toBe(15.99);
        });

        it('can update menu item category', function () {
            $newCategory = Category::factory()->create(['store_id' => $this->store->id]);

            Livewire::test(MenuEdit::class, ['menuItem' => $this->menuItem])
                ->set('category_id', $newCategory->id)
                ->call('save')
                ->assertHasNoErrors();

            $this->menuItem->refresh();
            expect($this->menuItem->category_id)->toBe($newCategory->id);
        });

        it('can toggle active status', function () {
            Livewire::test(MenuEdit::class, ['menuItem' => $this->menuItem])
                ->set('is_active', false)
                ->call('save')
                ->assertHasNoErrors();

            $this->menuItem->refresh();
            expect($this->menuItem->is_active)->toBeFalse();
        });

        it('can update menu item options', function () {
            $newOptions = [
                ['name' => 'Size', 'required' => true, 'choices' => [
                    ['name' => 'Small', 'price' => 0],
                    ['name' => 'Medium', 'price' => 2.00],
                    ['name' => 'Large', 'price' => 4.00]
                ]]
            ];

            Livewire::test(MenuEdit::class, ['menuItem' => $this->menuItem])
                ->set('options', $newOptions)
                ->call('save')
                ->assertHasNoErrors();

            $this->menuItem->refresh();
            expect($this->menuItem->options)->toBe($newOptions);
        });

        it('validates updated data', function () {
            Livewire::test(MenuEdit::class, ['menuItem' => $this->menuItem])
                ->set('name', '')
                ->set('price', -5.99)
                ->call('save')
                ->assertHasErrors(['name', 'price']);
        });

        it('prevents editing other stores menu items', function () {
            $otherStore = Store::factory()->create();
            $otherMenuItem = MenuItem::factory()->create(['store_id' => $otherStore->id]);

            Livewire::test(MenuEdit::class, ['menuItem' => $otherMenuItem])
                ->assertStatus(403);
        });
    });

    describe('Menu Item Availability', function () {
        beforeEach(function () {
            $this->menuItem = MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id,
                'is_active' => true
            ]);
        });

        it('shows active menu items to customers', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertSee($this->menuItem->name);
        });

        it('hides inactive menu items from customers', function () {
            $this->menuItem->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name);
        });

        it('hides menu items when store is inactive', function () {
            $this->store->update(['is_active' => false]);

            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name);
        });

        it('hides menu items when store is closed', function () {
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

            $this->get('/menu')
                ->assertDontSee($this->menuItem->name);
        });
    });
});
