<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserAddress;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and menu items
        $users = User::where('role', 'customer')->get();
        $menuItems = MenuItem::all();

        if ($users->isEmpty() || $menuItems->isEmpty()) {
            $this->command->warn('No customers or menu items found. Please run UserSeeder and MenuItemSeeder first.');
            return;
        }

        // Create 20 orders with realistic data based on menu configuration
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $orderDate = fake()->dateTimeBetween('-30 days', 'now');

            // Decide if this order is a delivery and resolve default address
            $status = fake()->randomElement(['pending', 'preparing', 'delivering', 'completed', 'cancelled']);
            $deliver = fake()->boolean(70); // 70% of seeded orders are delivery
            $address = null;
            if ($deliver) {
                $address = UserAddress::where('user_id', $user->id)->where('is_default', true)->first()
                    ?? UserAddress::where('user_id', $user->id)->first();
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address?->id,
                'code' => strtoupper(fake()->unique()->lexify('??????')),
                'status' => $status,
                'subtotal' => 0, // Will be calculated
                'tax' => 0, // Will be calculated
                'total' => 0, // Will be calculated
                'payment_status' => fake()->randomElement(['unpaid', 'paid']),
                'payment_provider' => fake()->randomElement(['stripe', null]),
                'payment_ref' => fake()->optional()->uuid(),
                'tracking_url' => $status === 'delivering' ? fake()->url() : null,
                'notes' => fake()->optional()->sentence(),
                'cancellation_remarks' => $status === 'cancelled' ? fake()->sentence() : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
                'ship_recipient_name' => $address?->recipient_name,
                'ship_phone' => $address?->phone,
                'ship_line1' => $address?->line1,
                'ship_line2' => $address?->line2,
                'ship_city' => $address?->city,
                'ship_state' => $address?->state,
                'ship_postal_code' => $address?->postal_code,
                'ship_country' => $address?->country,
            ]);

            // Create 1-4 order items per order, building selections from menu item
            $itemCount = fake()->numberBetween(1, 4);
            $subtotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $menuItem = $menuItems->random();
                $qty = fake()->numberBetween(1, 3);
                // derive selections and addon total
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

            // Update order totals
            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
        }

        $this->command->info('Created 20 orders with order items and realistic selections.');
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
                $one = collect($pool)->shuffle()->first();
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
