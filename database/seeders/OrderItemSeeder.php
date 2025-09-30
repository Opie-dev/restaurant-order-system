<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\UserAddress;
use App\Models\Store;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some menu items scoped to first store if exists
        $storeId = Store::first()?->id;
        $menuItems = MenuItem::when($storeId, fn($q) => $q->where('store_id', $storeId))->take(5)->get();

        if ($menuItems->isEmpty()) {
            $this->command->info('No menu items found. Please run MenuItemSeeder first.');
            return;
        }

        // Create a test user if none exists
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'role' => 'customer'
            ]);
        }

        // Build 3 realistic orders from actual menu configuration
        $ordersToCreate = [
            ['code' => 'ORD001', 'status' => 'pending', 'payment_status' => 'paid', 'items' => 1],
            ['code' => 'ORD002', 'status' => 'delivering', 'payment_status' => 'paid', 'items' => 2],
            ['code' => 'ORD003', 'status' => 'preparing', 'payment_status' => 'paid', 'items' => 1],
        ];

        foreach ($ordersToCreate as $orderMeta) {
            // Use default address if exists to simulate delivery checkout snapshot
            $address = UserAddress::where('user_id', $user->id)->where('is_default', true)->first()
                ?? UserAddress::where('user_id', $user->id)->first();

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address?->id,
                'code' => $orderMeta['code'],
                'status' => $orderMeta['status'],
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
                'payment_status' => $orderMeta['payment_status'],
                'payment_provider' => 'stripe',
                'payment_ref' => 'pi_' . strtolower($orderMeta['code']),
                'notes' => 'Please deliver to front door',
                'tracking_url' => $orderMeta['status'] === 'delivering' ? fake()->url() : null,
                'ship_recipient_name' => $address?->recipient_name,
                'ship_phone' => $address?->phone,
                'ship_line1' => $address?->line1,
                'ship_line2' => $address?->line2,
                'ship_city' => $address?->city,
                'ship_state' => $address?->state,
                'ship_postal_code' => $address?->postal_code,
                'ship_country' => $address?->country,
                'store_id' => $storeId,
            ]);

            $subtotal = 0;
            for ($i = 0; $i < $orderMeta['items']; $i++) {
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
                ]);
            }

            $tax = round($subtotal * 0.06, 2); // 6% tax
            $total = $subtotal + $tax;
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
        }

        $this->command->info('Created sample orders with realistic selections and pricing.');
    }

    /**
     * Build selections payload from a MenuItem's configured options/addons
     * and compute total addon price.
     *
     * @return array{0: array, 1: float}
     */
    protected function buildSelectionsFromMenuItem(MenuItem $menuItem): array
    {
        $selections = [
            'options' => [],
            'addons' => [],
        ];

        $addonsTotal = 0.0;

        // Options groups (no price impact)
        $optionGroups = $menuItem->options ?? [];
        foreach ($optionGroups as $group) {
            if (empty($group['options'])) {
                continue;
            }

            $rule = implode('|', $group['rules'] ?? []);
            $chooseMultiple = str_contains($rule, 'multiple');
            $pool = $group['options'];

            if ($chooseMultiple) {
                $picked = collect($pool)->shuffle()->take(fake()->numberBetween(1, min(2, count($pool))))
                    ->map(fn($opt) => ['name' => $opt['name']])->values()->all();
            } else {
                $one = Arr::first(collect($pool)->shuffle()->all());
                $picked = $one ? [['name' => $one['name']]] : [];
            }

            if (!empty($picked)) {
                $selections['options'][] = [
                    'name' => $group['name'] ?? 'Options',
                    'options' => $picked,
                ];
            }
        }

        // Addons groups (may carry price)
        $addonGroups = $menuItem->addons ?? [];
        foreach ($addonGroups as $group) {
            // Some set groups may contain both 'options' and 'addons'
            if (!empty($group['options'])) {
                $pool = $group['options'];
                $picked = collect($pool)->shuffle()->take(fake()->numberBetween(0, min(2, count($pool))))
                    ->map(function ($opt) use (&$addonsTotal) {
                        $price = isset($opt['price']) ? (float) $opt['price'] : 0.0;
                        $addonsTotal += $price;
                        return ['name' => $opt['name'], 'price' => $price];
                    })->values()->all();

                if (!empty($picked)) {
                    $selections['addons'][] = [
                        'name' => $group['name'] ?? 'Addons',
                        'options' => $picked,
                    ];
                }
            }

            if (!empty($group['addons'])) {
                $pool = $group['addons'];
                $picked = collect($pool)->shuffle()->take(fake()->numberBetween(0, min(2, count($pool))))
                    ->map(function ($opt) use (&$addonsTotal) {
                        $price = isset($opt['price']) ? (float) $opt['price'] : 0.0;
                        $addonsTotal += $price;
                        return ['name' => $opt['name'], 'price' => $price];
                    })->values()->all();

                if (!empty($picked)) {
                    $selections['addons'][] = [
                        'name' => $group['name'] ?? 'Addons',
                        'options' => $picked,
                    ];
                }
            }
        }

        return [$selections, $addonsTotal];
    }
}
