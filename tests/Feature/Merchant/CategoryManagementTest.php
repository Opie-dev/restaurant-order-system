<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Livewire;
use App\Livewire\Admin\Categories\CategoryList;
use App\Livewire\Admin\Categories\CategoryCreate;
use App\Livewire\Admin\Categories\CategoryEdit;

describe('Category Management', function () {
    beforeEach(function () {
        $this->merchant = User::factory()->create(['role' => 'admin']);
        $this->store = Store::factory()->create(['admin_id' => $this->merchant->id]);
        $this->actingAs($this->merchant);
    });

    describe('Category List', function () {
        beforeEach(function () {
            $this->category1 = Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'Appetizers',
                'is_active' => true
            ]);

            $this->category2 = Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'Main Courses',
                'is_active' => true
            ]);

            $this->category3 = Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'Desserts',
                'is_active' => false
            ]);
        });

        it('displays all categories', function () {
            Livewire::test(CategoryList::class)
                ->assertSee('Appetizers')
                ->assertSee('Main Courses')
                ->assertSee('Desserts');
        });

        it('shows category status', function () {
            Livewire::test(CategoryList::class)
                ->assertSee('Active')
                ->assertSee('Inactive');
        });

        it('shows menu item count for each category', function () {
            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category1->id
            ]);

            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category1->id
            ]);

            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category2->id
            ]);

            Livewire::test(CategoryList::class)
                ->assertSee('2 items')
                ->assertSee('1 item')
                ->assertSee('0 items');
        });

        it('can search categories by name', function () {
            Livewire::test(CategoryList::class)
                ->set('search', 'Appetizers')
                ->assertSee('Appetizers')
                ->assertDontSee('Main Courses')
                ->assertDontSee('Desserts');
        });

        it('can filter by active status', function () {
            Livewire::test(CategoryList::class)
                ->set('status_filter', 'active')
                ->assertSee('Appetizers')
                ->assertSee('Main Courses')
                ->assertDontSee('Desserts');
        });

        it('can filter by inactive status', function () {
            Livewire::test(CategoryList::class)
                ->set('status_filter', 'inactive')
                ->assertDontSee('Appetizers')
                ->assertDontSee('Main Courses')
                ->assertSee('Desserts');
        });

        it('can sort by name', function () {
            Livewire::test(CategoryList::class)
                ->set('sort_by', 'name')
                ->set('sort_direction', 'asc')
                ->assertSeeInOrder(['Appetizers', 'Desserts', 'Main Courses']);
        });

        it('can sort by item count', function () {
            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category2->id
            ]);

            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category2->id
            ]);

            Livewire::test(CategoryList::class)
                ->set('sort_by', 'item_count')
                ->set('sort_direction', 'desc')
                ->assertSeeInOrder(['Main Courses', 'Appetizers', 'Desserts']);
        });

        it('can toggle category active status', function () {
            Livewire::test(CategoryList::class)
                ->call('toggleActive', $this->category1->id)
                ->assertHasNoErrors();

            $this->category1->refresh();
            expect($this->category1->is_active)->toBeFalse();
        });

        it('can delete category', function () {
            Livewire::test(CategoryList::class)
                ->call('deleteCategory', $this->category3->id)
                ->assertHasNoErrors();

            expect(Category::find($this->category3->id))->toBeNull();
        });

        it('prevents deleting category with menu items', function () {
            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category1->id
            ]);

            Livewire::test(CategoryList::class)
                ->call('deleteCategory', $this->category1->id)
                ->assertHasErrors();

            expect(Category::find($this->category1->id))->not->toBeNull();
        });
    });

    describe('Category Creation', function () {
        it('can create a new category', function () {
            Livewire::test(CategoryCreate::class)
                ->set('name', 'Beverages')
                ->set('description', 'Drinks and beverages')
                ->set('is_active', true)
                ->call('save')
                ->assertHasNoErrors()
                ->assertRedirect();

            $category = Category::where('name', 'Beverages')->first();
            expect($category)->not->toBeNull();
            expect($category->store_id)->toBe($this->store->id);
            expect($category->description)->toBe('Drinks and beverages');
            expect($category->is_active)->toBeTrue();
        });

        it('validates required fields', function () {
            Livewire::test(CategoryCreate::class)
                ->call('save')
                ->assertHasErrors(['name']);
        });

        it('validates unique category name per store', function () {
            Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'Existing Category'
            ]);

            Livewire::test(CategoryCreate::class)
                ->set('name', 'Existing Category')
                ->call('save')
                ->assertHasErrors(['name']);
        });

        it('allows same category name in different stores', function () {
            $otherStore = Store::factory()->create();
            Category::factory()->create([
                'store_id' => $otherStore->id,
                'name' => 'Common Category'
            ]);

            Livewire::test(CategoryCreate::class)
                ->set('name', 'Common Category')
                ->call('save')
                ->assertHasNoErrors();

            expect(Category::where('name', 'Common Category')->count())->toBe(2);
        });

        it('validates category name length', function () {
            Livewire::test(CategoryCreate::class)
                ->set('name', 'A') // Too short
                ->call('save')
                ->assertHasErrors(['name']);

            Livewire::test(CategoryCreate::class)
                ->set('name', str_repeat('A', 256)) // Too long
                ->call('save')
                ->assertHasErrors(['name']);
        });

        it('can upload category image', function () {
            $file = \Illuminate\Http\UploadedFile::fake()->image('category.jpg', 400, 300);

            Livewire::test(CategoryCreate::class)
                ->set('name', 'Category with Image')
                ->set('image', $file)
                ->call('save')
                ->assertHasNoErrors();

            $category = Category::where('name', 'Category with Image')->first();
            expect($category->image_path)->not->toBeNull();
        });

        it('can set category display order', function () {
            Livewire::test(CategoryCreate::class)
                ->set('name', 'First Category')
                ->set('display_order', 1)
                ->call('save')
                ->assertHasNoErrors();

            $category = Category::where('name', 'First Category')->first();
            expect($category->display_order)->toBe(1);
        });
    });

    describe('Category Editing', function () {
        beforeEach(function () {
            $this->category = Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'Original Category',
                'description' => 'Original description',
                'is_active' => true
            ]);
        });

        it('can update category details', function () {
            Livewire::test(CategoryEdit::class, ['category' => $this->category])
                ->set('name', 'Updated Category')
                ->set('description', 'Updated description')
                ->call('save')
                ->assertHasNoErrors();

            $this->category->refresh();
            expect($this->category->name)->toBe('Updated Category');
            expect($this->category->description)->toBe('Updated description');
        });

        it('can toggle active status', function () {
            Livewire::test(CategoryEdit::class, ['category' => $this->category])
                ->set('is_active', false)
                ->call('save')
                ->assertHasNoErrors();

            $this->category->refresh();
            expect($this->category->is_active)->toBeFalse();
        });

        it('can update category image', function () {
            $file = \Illuminate\Http\UploadedFile::fake()->image('new-category.jpg', 400, 300);

            Livewire::test(CategoryEdit::class, ['category' => $this->category])
                ->set('image', $file)
                ->call('save')
                ->assertHasNoErrors();

            $this->category->refresh();
            expect($this->category->image_path)->not->toBeNull();
        });

        it('can update display order', function () {
            Livewire::test(CategoryEdit::class, ['category' => $this->category])
                ->set('display_order', 5)
                ->call('save')
                ->assertHasNoErrors();

            $this->category->refresh();
            expect($this->category->display_order)->toBe(5);
        });

        it('validates updated data', function () {
            Livewire::test(CategoryEdit::class, ['category' => $this->category])
                ->set('name', '')
                ->call('save')
                ->assertHasErrors(['name']);
        });

        it('validates unique name when updating', function () {
            $otherCategory = Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'Other Category'
            ]);

            Livewire::test(CategoryEdit::class, ['category' => $this->category])
                ->set('name', 'Other Category')
                ->call('save')
                ->assertHasErrors(['name']);
        });

        it('allows keeping same name when updating', function () {
            Livewire::test(CategoryEdit::class, ['category' => $this->category])
                ->set('name', 'Original Category') // Same name
                ->set('description', 'Updated description')
                ->call('save')
                ->assertHasNoErrors();

            $this->category->refresh();
            expect($this->category->description)->toBe('Updated description');
        });

        it('prevents editing other stores categories', function () {
            $otherStore = Store::factory()->create();
            $otherCategory = Category::factory()->create(['store_id' => $otherStore->id]);

            Livewire::test(CategoryEdit::class, ['category' => $otherCategory])
                ->assertStatus(403);
        });
    });

    describe('Category Relationships', function () {
        beforeEach(function () {
            $this->category = Category::factory()->create([
                'store_id' => $this->store->id
            ]);
        });

        it('belongs to store', function () {
            expect($this->category->store)->toBeInstanceOf(Store::class);
            expect($this->category->store->id)->toBe($this->store->id);
        });

        it('has many menu items', function () {
            $menuItem1 = MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id
            ]);

            $menuItem2 = MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id
            ]);

            expect($this->category->menuItems)->toHaveCount(2);
            expect($this->category->menuItems->pluck('id')->toArray())
                ->toContain($menuItem1->id, $menuItem2->id);
        });

        it('can get active menu items', function () {
            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id,
                'is_active' => true
            ]);

            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id,
                'is_active' => false
            ]);

            expect($this->category->activeMenuItems)->toHaveCount(1);
        });

        it('can get menu item count', function () {
            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id
            ]);

            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category->id
            ]);

            expect($this->category->menuItemsCount)->toBe(2);
        });
    });

    describe('Category Display Order', function () {
        beforeEach(function () {
            $this->category1 = Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'First Category',
                'display_order' => 1
            ]);

            $this->category2 = Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'Second Category',
                'display_order' => 2
            ]);

            $this->category3 = Category::factory()->create([
                'store_id' => $this->store->id,
                'name' => 'Third Category',
                'display_order' => 3
            ]);
        });

        it('displays categories in correct order', function () {
            Livewire::test(CategoryList::class)
                ->assertSeeInOrder([
                    'First Category',
                    'Second Category',
                    'Third Category'
                ]);
        });

        it('can reorder categories', function () {
            Livewire::test(CategoryList::class)
                ->call('reorderCategories', [
                    $this->category3->id,
                    $this->category1->id,
                    $this->category2->id
                ])
                ->assertHasNoErrors();

            $this->category1->refresh();
            $this->category2->refresh();
            $this->category3->refresh();

            expect($this->category3->display_order)->toBe(1);
            expect($this->category1->display_order)->toBe(2);
            expect($this->category2->display_order)->toBe(3);
        });

        it('can move category up', function () {
            Livewire::test(CategoryList::class)
                ->call('moveUp', $this->category2->id)
                ->assertHasNoErrors();

            $this->category1->refresh();
            $this->category2->refresh();

            expect($this->category2->display_order)->toBe(1);
            expect($this->category1->display_order)->toBe(2);
        });

        it('can move category down', function () {
            Livewire::test(CategoryList::class)
                ->call('moveDown', $this->category1->id)
                ->assertHasNoErrors();

            $this->category1->refresh();
            $this->category2->refresh();

            expect($this->category1->display_order)->toBe(2);
            expect($this->category2->display_order)->toBe(1);
        });
    });

    describe('Category Access Control', function () {
        it('prevents non-admin access to category management', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/admin/categories')
                ->assertStatus(403);
        });

        it('prevents access to other stores categories', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create(['admin_id' => $otherMerchant->id]);
            $otherCategory = Category::factory()->create(['store_id' => $otherStore->id]);

            $this->actingAs($otherMerchant);

            Livewire::test(CategoryList::class)
                ->assertDontSee($this->store->name);
        });

        it('requires authentication for category access', function () {
            auth()->logout();

            $this->get('/admin/categories')
                ->assertRedirect('/login');
        });
    });

    describe('Category Bulk Operations', function () {
        beforeEach(function () {
            $this->category1 = Category::factory()->create([
                'store_id' => $this->store->id,
                'is_active' => true
            ]);

            $this->category2 = Category::factory()->create([
                'store_id' => $this->store->id,
                'is_active' => true
            ]);

            $this->category3 = Category::factory()->create([
                'store_id' => $this->store->id,
                'is_active' => false
            ]);
        });

        it('can activate multiple categories', function () {
            Livewire::test(CategoryList::class)
                ->call('bulkActivate', [$this->category3->id])
                ->assertHasNoErrors();

            $this->category3->refresh();
            expect($this->category3->is_active)->toBeTrue();
        });

        it('can deactivate multiple categories', function () {
            Livewire::test(CategoryList::class)
                ->call('bulkDeactivate', [$this->category1->id, $this->category2->id])
                ->assertHasNoErrors();

            $this->category1->refresh();
            $this->category2->refresh();

            expect($this->category1->is_active)->toBeFalse();
            expect($this->category2->is_active)->toBeFalse();
        });

        it('can delete multiple categories', function () {
            Livewire::test(CategoryList::class)
                ->call('bulkDelete', [$this->category3->id])
                ->assertHasNoErrors();

            expect(Category::find($this->category3->id))->toBeNull();
        });

        it('prevents bulk deleting categories with menu items', function () {
            MenuItem::factory()->create([
                'store_id' => $this->store->id,
                'category_id' => $this->category1->id
            ]);

            Livewire::test(CategoryList::class)
                ->call('bulkDelete', [$this->category1->id, $this->category2->id])
                ->assertHasErrors();

            expect(Category::find($this->category1->id))->not->toBeNull();
            expect(Category::find($this->category2->id))->not->toBeNull();
        });
    });
});
