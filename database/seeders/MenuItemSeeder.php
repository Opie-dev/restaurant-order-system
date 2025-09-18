<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        //clear data
        // DB::table('menu_items')->truncate();
        // DB::table('categories')->truncate();
        // Storage::disk('public')->deleteDirectory('menu');

        // Ensure storage/app/public/menu exists (served via public/storage/menu after storage:link)
        Storage::disk('public')->makeDirectory('menu');

        $data = [
            'Main Dishes' => [
                'Pizzas' => [
                    ['Margherita', 9.99, 'Classic tomato and mozzarella pizza'],
                    ['Pepperoni', 11.49, 'Spicy pepperoni with melted cheese'],
                    ['Hawaiian', 12.99, 'Ham and pineapple pizza'],
                ],
                'Burgers' => [
                    ['Classic Burger', 8.49, 'Beef patty with lettuce and tomato'],
                    ['Cheese Burger', 8.99, 'Classic burger with cheddar cheese'],
                    ['Bacon Burger', 10.99, 'Burger with crispy bacon and cheese'],
                ],
                'Pasta' => [
                    ['Spaghetti Bolognese', 11.99, 'Classic meat sauce pasta'],
                    ['Fettuccine Alfredo', 12.99, 'Creamy parmesan sauce pasta'],
                ]
            ],
            'Sides' => [
                'Salads' => [
                    ['Caesar Salad', 7.49, 'Romaine lettuce with caesar dressing'],
                    ['Greek Salad', 7.99, 'Mixed vegetables with feta cheese'],
                    ['Garden Salad', 6.99, 'Fresh mixed greens with vinaigrette'],
                ],
                'Appetizers' => [
                    ['Garlic Bread', 4.99, 'Toasted bread with garlic butter'],
                    ['Mozzarella Sticks', 5.99, 'Breaded and fried cheese sticks'],
                ]
            ],
            'Beverages' => [
                'Soft Drinks' => [
                    ['Cola', 1.99, 'Classic cola drink'],
                    ['Sprite', 1.99, 'Lemon-lime soda'],
                ],
                'Juices' => [
                    ['Orange Juice', 2.49, 'Fresh squeezed orange juice'],
                    ['Apple Juice', 2.49, 'Pure apple juice'],
                ]
            ]
        ];

        foreach ($data as $categoryName => $subcategories) {
            // Create main category
            $mainCategory = Category::firstOrCreate(
                ['name' => $categoryName],
                ['is_active' => true]
            );

            foreach ($subcategories as $subcategoryName => $items) {
                // Create subcategory under main category
                $subcategory = Category::firstOrCreate(
                    ['name' => $subcategoryName],
                    [
                        'is_active' => true,
                        'parent_id' => $mainCategory->id,
                    ]
                );

                $position = (int) (MenuItem::where('category_id', $subcategory->id)->max('position') ?? 0);

                foreach ($items as [$name, $price, $description]) {
                    $position++;
                    $seed = Str::slug($name . '-' . $subcategoryName);

                    $imagePath = null;
                    try {
                        $bytes = @file_get_contents("https://picsum.photos/seed/{$seed}/640/360");
                        if ($bytes !== false) {
                            $relative = 'menu/' . $seed . '.jpg';
                            Storage::disk('public')->put($relative, $bytes);
                            $imagePath = $relative;
                        }
                    } catch (\Throwable $e) {
                        // Ignore image download errors; keep image_path null
                    }

                    MenuItem::firstOrCreate(
                        [
                            'name' => $name,
                            'category_id' => $subcategory->id,
                        ],
                        [
                            'description' => $description,
                            'price' => $price,
                            'is_active' => true,
                            'image_path' => $imagePath,
                            'position' => $position,
                        ]
                    );
                }
            }
        }
    }
}
