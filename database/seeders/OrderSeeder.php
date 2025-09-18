<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        // Create 20 orders with realistic data
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $orderDate = fake()->dateTimeBetween('-30 days', 'now');

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'code' => strtoupper(fake()->unique()->lexify('??????')),
                'status' => fake()->randomElement(['pending', 'confirmed', 'preparing', 'ready', 'completed']),
                'subtotal' => 0, // Will be calculated
                'tax' => 0, // Will be calculated
                'total' => 0, // Will be calculated
                'payment_status' => fake()->randomElement(['unpaid', 'paid']),
                'payment_provider' => fake()->randomElement(['stripe', null]),
                'payment_ref' => fake()->optional()->uuid(),
                'notes' => fake()->optional()->sentence(),
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Create 1-4 order items per order
            $itemCount = fake()->numberBetween(1, 4);
            $subtotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $menuItem = $menuItems->random();
                $qty = fake()->numberBetween(1, 3);
                $unitPrice = $menuItem->price;
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'name_snapshot' => $menuItem->name,
                    'unit_price' => $unitPrice,
                    'qty' => $qty,
                    'line_total' => $lineTotal,
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

        $this->command->info('Created 20 orders with order items.');
    }
}
