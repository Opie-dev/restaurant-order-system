<?php

namespace Database\Seeders;

use App\Models\CartItem;
use App\Models\MenuItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Cart;

class CartsComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        if (Store::count() === 0 || MenuItem::count() === 0) {
            $this->call([
                MerchantSeeder::class,
                StoresComprehensiveSeeder::class,
                CategoriesComprehensiveSeeder::class,
                MenuItemsComprehensiveSeeder::class,
            ]);
        }

        $customers = User::where('role', 'customer')->get();
        if ($customers->isEmpty()) {
            $this->call(CustomersSeeder::class);
            $customers = User::where('role', 'customer')->get();
        }

        foreach ($customers as $customer) {
            $store = Store::inRandomOrder()->first();
            if (!$store) {
                continue;
            }
            // Ensure a cart exists for this user and store
            $cart = Cart::firstOrCreate([
                'user_id' => $customer->id,
                'store_id' => $store->id,
            ]);

            $items = MenuItem::where('store_id', $store->id)
                ->inRandomOrder()
                ->take(fake()->numberBetween(0, 5))
                ->get();

            foreach ($items as $menuItem) {
                [$selections, $addonsTotal] = $this->buildSelectionsFromMenuItem($menuItem);
                CartItem::create([
                    'cart_id' => $cart->id,
                    'menu_item_id' => $menuItem->id,
                    'qty' => fake()->numberBetween(1, 3),
                    'unit_price' => (float) $menuItem->price + $addonsTotal,
                    'selections' => $selections,
                ]);
            }
        }

        $this->command?->info('CartsComprehensiveSeeder: cart items created for customers.');
    }

    private function buildSelectionsFromMenuItem(MenuItem $menuItem): array
    {
        $selections = ['options' => [], 'addons' => []];
        $addonsTotal = 0.0;

        foreach ($menuItem->options ?? [] as $group) {
            $pool = $group['options'] ?? [];
            if (!$pool) continue;
            $rules = $group['rules'] ?? [];
            $multiple = in_array('multiple', $rules);
            if ($multiple) {
                $picked = collect($pool)->shuffle()->take(fake()->numberBetween(1, min(2, count($pool))))
                    ->map(fn($o) => ['name' => $o['name']])->values()->all();
            } else {
                $one = collect($pool)->shuffle()->first();
                $picked = $one ? [['name' => $one['name']]] : [];
            }
            if ($picked) {
                $selections['options'][] = ['name' => $group['name'] ?? 'Options', 'options' => $picked];
            }
        }

        foreach ($menuItem->addons ?? [] as $group) {
            $pool = $group['options'] ?? [];
            if (!$pool) continue;
            $picked = collect($pool)->shuffle()->take(fake()->numberBetween(0, min(2, count($pool))))
                ->map(function ($o) use (&$addonsTotal) {
                    $price = isset($o['price']) ? (float) $o['price'] : 0.0;
                    $addonsTotal += $price;
                    return ['name' => $o['name'], 'price' => $price];
                })->values()->all();
            if ($picked) {
                $selections['addons'][] = ['name' => $group['name'] ?? 'Addons', 'options' => $picked];
            }
        }

        return [$selections, $addonsTotal];
    }
}
