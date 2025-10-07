<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Database\Seeder;

class CategoriesComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::all();
        if ($stores->isEmpty()) {
            $this->call([MerchantSeeder::class, StoresComprehensiveSeeder::class]);
            $stores = Store::all();
        }

        $categoryTemplates = [
            'Restaurant' => ['Appetizers', 'Main Courses', 'Rice & Noodles', 'Seafood', 'Vegetables', 'Desserts', 'Beverages'],
            'Cafe' => ['Coffee', 'Tea', 'Pastries', 'Sandwiches', 'Salads', 'Smoothies', 'Snacks'],
            'Fast Food' => ['Burgers', 'Fried Chicken', 'Fries', 'Wraps', 'Sides', 'Drinks', 'Desserts'],
            'Kitchen' => ['Rice Dishes', 'Curry', 'Stir Fry', 'Soup', 'Vegetables', 'Meat', 'Fish'],
            'Bistro' => ['Starters', 'Mains', 'Pasta', 'Pizza', 'Salads', 'Wine', 'Desserts'],
        ];

        foreach ($stores as $store) {
            $type = $this->inferType($store->name);
            $names = $categoryTemplates[$type] ?? $categoryTemplates['Restaurant'];

            foreach ($names as $name) {
                Category::firstOrCreate([
                    'store_id' => $store->id,
                    'name' => $name,
                ], [
                    'is_active' => true,
                ]);
            }
        }

        $this->command?->info('CategoriesComprehensiveSeeder: categories ensured.');
    }

    private function inferType(string $name): string
    {
        $n = strtolower($name);
        if (str_contains($n, 'cafe')) return 'Cafe';
        if (str_contains($n, 'fast') || str_contains($n, 'food')) return 'Fast Food';
        if (str_contains($n, 'kitchen')) return 'Kitchen';
        if (str_contains($n, 'bistro')) return 'Bistro';
        return 'Restaurant';
    }
}
