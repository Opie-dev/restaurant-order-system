<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class OrdersComprehensiveSeeder extends Seeder
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

        foreach (Store::all() as $store) {
            $menuItems = MenuItem::where('store_id', $store->id)->get();
            $orderCount = fake()->numberBetween(15, 30);
            for ($i = 0; $i < $orderCount; $i++) {
                $this->createOrder($store, $customers, $menuItems);
            }
        }

        $this->command?->info('OrdersComprehensiveSeeder: orders created for all stores.');
    }

    private function createOrder(Store $store, $customers, $menuItems): void
    {
        $customer = $customers->random();
        $orderDate = fake()->dateTimeBetween('-30 days', 'now');
        $status = fake()->randomElement(['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled']);
        $isDelivery = fake()->boolean(70);

        $address = null;
        if ($isDelivery) {
            $address = UserAddress::where('user_id', $customer->id)->where('is_default', true)->first()
                ?? UserAddress::where('user_id', $customer->id)->first();
        }

        $order = Order::create([
            'user_id' => $customer->id,
            'store_id' => $store->id,
            'code' => strtoupper(fake()->unique()->lexify('??????')),
            'status' => $status,
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'payment_status' => fake()->randomElement(['unpaid', 'paid', 'processing']),
            'payment_provider' => fake()->randomElement(['stripe', 'cash', null]),
            'payment_ref' => fake()->optional()->uuid(),
            'delivery_fee' => $isDelivery ? fake()->randomFloat(2, 3.00, 8.00) : null,
            'notes' => fake()->optional()->sentence(),
            'cancellation_remarks' => $status === 'cancelled' ? fake()->sentence() : null,
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
        ]);

        $itemCount = fake()->numberBetween(1, 4);
        $subtotal = 0;

        for ($j = 0; $j < $itemCount; $j++) {
            $menuItem = $menuItems->random();
            $qty = fake()->numberBetween(1, 3);
            [$selections, $addonsTotal] = $this->buildSelectionsFromMenuItem($menuItem);
            $unitPrice = (float) $menuItem->price + $addonsTotal;
            $lineTotal = $unitPrice * $qty;
            $subtotal += $lineTotal;

            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'name_snapshot' => $menuItem->name,
                'unit_price' => $unitPrice,
                'qty' => $qty,
                'line_total' => $lineTotal,
                'selections' => $selections,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }

        $tax = $subtotal * 0.08;
        $deliveryFee = $order->delivery_fee ?? 0;
        $total = $subtotal + $tax + $deliveryFee;
        $order->update(['subtotal' => $subtotal, 'tax' => $tax, 'total' => $total]);
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
