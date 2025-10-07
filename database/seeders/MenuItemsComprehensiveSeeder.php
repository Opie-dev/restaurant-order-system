<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Store;
use Illuminate\Database\Seeder;

class MenuItemsComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        if (Store::count() === 0 || Category::count() === 0) {
            $this->call([MerchantSeeder::class, StoresComprehensiveSeeder::class, CategoriesComprehensiveSeeder::class]);
        }

        $stores = Store::all();
        foreach ($stores as $store) {
            $categories = Category::where('store_id', $store->id)->get();
            foreach ($categories as $category) {
                $count = fake()->numberBetween(3, 8);
                for ($i = 0; $i < $count; $i++) {
                    $this->createItem($store->id, $category->id);
                }
            }
        }

        $this->command?->info('MenuItemsComprehensiveSeeder: menu items ensured.');
    }

    private function createItem(int $storeId, int $categoryId): void
    {
        $basePrice = fake()->randomFloat(2, 5.00, 25.00);
        MenuItem::create([
            'store_id' => $storeId,
            'category_id' => $categoryId,
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(8),
            'price' => $basePrice,
            'base_price' => $basePrice,
            'options' => $this->randomOptions(),
            'addons' => $this->randomAddons(),
            'is_active' => fake()->boolean(90),
            'stock' => fake()->numberBetween(0, 100),
        ]);
    }

    private function randomOptions(): ?array
    {
        if (!fake()->boolean(60)) return null;
        $groups = [
            'Size' => [['name' => 'Small'], ['name' => 'Medium'], ['name' => 'Large']],
            'Spice Level' => [['name' => 'Mild'], ['name' => 'Medium'], ['name' => 'Hot'], ['name' => 'Extra Hot']],
            'Cooking' => [['name' => 'Grilled'], ['name' => 'Fried'], ['name' => 'Steamed'], ['name' => 'Baked']],
        ];
        $selected = collect($groups)->random(fake()->numberBetween(1, 2));
        $out = [];
        foreach ($selected as $name => $opts) {
            $out[] = [
                'name' => $name,
                'enabled' => true,
                'rules' => fake()->randomElement([
                    ['required', 'one'],
                    ['required', 'multiple'],
                    ['optional', 'one'],
                    ['optional', 'multiple'],
                ]),
                'options' => $opts,
            ];
        }
        return $out;
    }

    private function randomAddons(): ?array
    {
        if (!fake()->boolean(50)) return null;
        $groups = [
            'Extras' => [
                ['name' => 'Extra Cheese', 'price' => fake()->randomFloat(2, 1, 3)],
                ['name' => 'Extra Meat', 'price' => fake()->randomFloat(2, 2, 5)],
                ['name' => 'Veggies', 'price' => fake()->randomFloat(2, 0.5, 2)],
            ],
            'Sides' => [
                ['name' => 'Fries', 'price' => fake()->randomFloat(2, 2, 4)],
                ['name' => 'Onion Rings', 'price' => fake()->randomFloat(2, 2.5, 4.5)],
                ['name' => 'Side Salad', 'price' => fake()->randomFloat(2, 3, 5)],
            ],
        ];
        $selected = collect($groups)->random(fake()->numberBetween(1, 2));
        $out = [];
        foreach ($selected as $name => $opts) {
            $out[] = [
                'name' => $name,
                'enabled' => true,
                'rules' => ['optional', 'multiple'],
                'options' => $opts,
            ];
        }
        return $out;
    }
}
