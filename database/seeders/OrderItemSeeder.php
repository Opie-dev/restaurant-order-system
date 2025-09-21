<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some menu items
        $menuItems = MenuItem::take(5)->get();

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

        // Create sample orders with selections
        $orders = [
            [
                'code' => 'ORD001',
                'status' => 'pending',
                'payment_status' => 'paid',
                'items' => [
                    [
                        'menu_item' => $menuItems->first(),
                        'qty' => 2,
                        'selections' => [
                            'options' => ['Medium', 'Extra Spicy'],
                            'addons' => ['Extra Cheese', 'Bacon']
                        ]
                    ]
                ]
            ],
            [
                'code' => 'ORD002',
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'items' => [
                    [
                        'menu_item' => $menuItems->skip(1)->first(),
                        'qty' => 1,
                        'selections' => [
                            'options' => ['Large'],
                            'addons' => ['Extra Sauce']
                        ]
                    ],
                    [
                        'menu_item' => $menuItems->skip(2)->first(),
                        'qty' => 3,
                        'selections' => [
                            'options' => ['Small', 'Mild'],
                            'addons' => ['Extra Vegetables']
                        ]
                    ]
                ]
            ],
            [
                'code' => 'ORD003',
                'status' => 'preparing',
                'payment_status' => 'paid',
                'items' => [
                    [
                        'menu_item' => $menuItems->skip(3)->first(),
                        'qty' => 1,
                        'selections' => [
                            'options' => ['Regular'],
                            'addons' => ['Extra Meat', 'Extra Cheese', 'Side Salad']
                        ]
                    ]
                ]
            ]
        ];

        foreach ($orders as $orderData) {
            $order = Order::create([
                'user_id' => $user->id,
                'code' => $orderData['code'],
                'status' => $orderData['status'],
                'subtotal' => 50.00,
                'tax' => 5.00,
                'total' => 55.00,
                'payment_status' => $orderData['payment_status'],
                'payment_provider' => 'stripe',
                'payment_ref' => 'pi_' . strtolower($orderData['code']),
                'notes' => 'Please deliver to front door'
            ]);

            foreach ($orderData['items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $itemData['menu_item']->id,
                    'name_snapshot' => $itemData['menu_item']->name,
                    'unit_price' => $itemData['menu_item']->price,
                    'qty' => $itemData['qty'],
                    'line_total' => $itemData['menu_item']->price * $itemData['qty'],
                    'selections' => $itemData['selections']
                ]);
            }
        }

        $this->command->info('Created sample orders with options and addons.');
    }
}
