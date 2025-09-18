<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $roots = [
            'Pizzas' => [
                'Classic' => ['Margherita', 'Pepperoni'],
                'Special' => ['Four Cheese', 'BBQ Chicken'],
            ],
            'Burgers' => [
                'Beef' => ['Classic', 'Cheese'],
                'Chicken' => ['Grilled', 'Crispy'],
            ],
            'Salads' => [
                'Leafy' => ['Caesar', 'Greek'],
            ],
            'Pasta' => [],
            'Desserts' => [],
            'Drinks' => [],
        ];

        // Root positions
        $rootPos = 1;
        foreach ($roots as $rootName => $children) {
            $root = Category::firstOrCreate(['name' => $rootName, 'parent_id' => null], ['is_active' => true]);
            if ($root->position !== $rootPos) {
                $root->update(['position' => $rootPos]);
            }
            $rootPos++;

            // Level 2
            $childPos = 1;
            foreach ($children as $childName => $grandChildren) {
                $child = Category::firstOrCreate(['name' => $childName, 'parent_id' => $root->id], ['is_active' => true]);
                if ($child->position !== $childPos) {
                    $child->update(['position' => $childPos]);
                }
                $childPos++;

                // Level 3
                $grandPos = 1;
                foreach ($grandChildren as $grandName) {
                    $grand = Category::firstOrCreate(['name' => $grandName, 'parent_id' => $child->id], ['is_active' => true]);
                    if ($grand->position !== $grandPos) {
                        $grand->update(['position' => $grandPos]);
                    }
                    $grandPos++;
                }
            }
        }

        // Append any stray categories (without parent) at the end
        $others = Category::whereNull('parent_id')->whereNotIn('name', array_keys($roots))->orderBy('name')->get();
        foreach ($others as $other) {
            $other->update(['position' => $rootPos]);
            $rootPos++;
        }
    }
}
