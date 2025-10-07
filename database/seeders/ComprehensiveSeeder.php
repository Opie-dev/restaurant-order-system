<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting comprehensive seeding...');

        // Create multiple merchants
        $merchants = $this->createMerchants();

        // Create multiple stores for each merchant
        $stores = $this->createStores($merchants);

        // Create categories for each store
        $this->createCategories($stores);

        // Create menu items with options and addons for each store
        $this->createMenuItems($stores);

        // Create multiple customers
        $customers = $this->createCustomers();

        // Create addresses for customers
        $this->createAddresses($customers);

        // Create orders for each store
        $this->createOrders($stores, $customers);

        // Create cart items for customers
        $this->createCartItems($stores, $customers);

        $this->command->info('Comprehensive seeding completed!');
    }

    private function createMerchants(): array
    {
        $merchants = [];

        $merchantData = [
            ['name' => 'Ahmad Restaurant', 'email' => 'ahmad@restaurant.com'],
            ['name' => 'Sarah Cafe', 'email' => 'sarah@cafe.com'],
            ['name' => 'Hassan Food', 'email' => 'hassan@food.com'],
            ['name' => 'Fatima Kitchen', 'email' => 'fatima@kitchen.com'],
            ['name' => 'Omar Bistro', 'email' => 'omar@bistro.com'],
        ];

        foreach ($merchantData as $data) {
            $merchant = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                ]
            );
            $merchants[] = $merchant;
        }

        $this->command->info('Created ' . count($merchants) . ' merchants');
        return $merchants;
    }

    private function createStores(array $merchants): array
    {
        $stores = [];

        $storeData = [
            [
                'name' => 'Ahmad Restaurant - Main Branch',
                'slug' => 'ahmad-restaurant-main',
                'description' => 'Traditional Malaysian cuisine with modern twist',
                'phone' => '03-12345678',
                'email' => 'main@ahmadrestaurant.com',
                'address_line1' => '123 Jalan Ampang',
                'city' => 'Kuala Lumpur',
                'state' => 'Selangor',
                'postal_code' => '50450',
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '08:00', 'close' => '22:00'],
                        ['day' => 'Tuesday', 'enabled' => true, 'open' => '08:00', 'close' => '22:00'],
                        ['day' => 'Wednesday', 'enabled' => true, 'open' => '08:00', 'close' => '22:00'],
                        ['day' => 'Thursday', 'enabled' => true, 'open' => '08:00', 'close' => '22:00'],
                        ['day' => 'Friday', 'enabled' => true, 'open' => '08:00', 'close' => '23:00'],
                        ['day' => 'Saturday', 'enabled' => true, 'open' => '08:00', 'close' => '23:00'],
                        ['day' => 'Sunday', 'enabled' => true, 'open' => '08:00', 'close' => '21:00'],
                    ],
                    'always_open' => false
                ]
            ],
            [
                'name' => 'Sarah Cafe - Downtown',
                'slug' => 'sarah-cafe-downtown',
                'description' => 'Cozy cafe serving fresh coffee and light meals',
                'phone' => '03-87654321',
                'email' => 'downtown@sarahcafe.com',
                'address_line1' => '456 Jalan Bukit Bintang',
                'city' => 'Kuala Lumpur',
                'state' => 'Kuala Lumpur',
                'postal_code' => '50200',
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '07:00', 'close' => '20:00'],
                        ['day' => 'Tuesday', 'enabled' => true, 'open' => '07:00', 'close' => '20:00'],
                        ['day' => 'Wednesday', 'enabled' => true, 'open' => '07:00', 'close' => '20:00'],
                        ['day' => 'Thursday', 'enabled' => true, 'open' => '07:00', 'close' => '20:00'],
                        ['day' => 'Friday', 'enabled' => true, 'open' => '07:00', 'close' => '21:00'],
                        ['day' => 'Saturday', 'enabled' => true, 'open' => '08:00', 'close' => '21:00'],
                        ['day' => 'Sunday', 'enabled' => true, 'open' => '08:00', 'close' => '19:00'],
                    ],
                    'always_open' => false
                ]
            ],
            [
                'name' => 'Hassan Food - Subang',
                'slug' => 'hassan-food-subang',
                'description' => 'Halal fast food and traditional dishes',
                'phone' => '03-11223344',
                'email' => 'subang@hassanfood.com',
                'address_line1' => '789 Jalan SS15',
                'city' => 'Subang Jaya',
                'state' => 'Selangor',
                'postal_code' => '47500',
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '09:00', 'close' => '23:00'],
                        ['day' => 'Tuesday', 'enabled' => true, 'open' => '09:00', 'close' => '23:00'],
                        ['day' => 'Wednesday', 'enabled' => true, 'open' => '09:00', 'close' => '23:00'],
                        ['day' => 'Thursday', 'enabled' => true, 'open' => '09:00', 'close' => '23:00'],
                        ['day' => 'Friday', 'enabled' => true, 'open' => '09:00', 'close' => '24:00'],
                        ['day' => 'Saturday', 'enabled' => true, 'open' => '09:00', 'close' => '24:00'],
                        ['day' => 'Sunday', 'enabled' => true, 'open' => '09:00', 'close' => '22:00'],
                    ],
                    'always_open' => false
                ]
            ],
            [
                'name' => 'Fatima Kitchen - PJ',
                'slug' => 'fatima-kitchen-pj',
                'description' => 'Home-style cooking and family recipes',
                'phone' => '03-55667788',
                'email' => 'pj@fatimakitchen.com',
                'address_line1' => '321 Jalan 17/1',
                'city' => 'Petaling Jaya',
                'state' => 'Selangor',
                'postal_code' => '46400',
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '10:00', 'close' => '21:00'],
                        ['day' => 'Tuesday', 'enabled' => true, 'open' => '10:00', 'close' => '21:00'],
                        ['day' => 'Wednesday', 'enabled' => true, 'open' => '10:00', 'close' => '21:00'],
                        ['day' => 'Thursday', 'enabled' => true, 'open' => '10:00', 'close' => '21:00'],
                        ['day' => 'Friday', 'enabled' => true, 'open' => '10:00', 'close' => '22:00'],
                        ['day' => 'Saturday', 'enabled' => true, 'open' => '10:00', 'close' => '22:00'],
                        ['day' => 'Sunday', 'enabled' => false, 'open' => '', 'close' => ''],
                    ],
                    'always_open' => false
                ]
            ],
            [
                'name' => 'Omar Bistro - KLCC',
                'slug' => 'omar-bistro-klcc',
                'description' => 'Upscale dining with international cuisine',
                'phone' => '03-99887766',
                'email' => 'klcc@omarbistro.com',
                'address_line1' => '654 Jalan Ampang',
                'city' => 'Kuala Lumpur',
                'state' => 'Kuala Lumpur',
                'postal_code' => '50450',
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '11:00', 'close' => '23:00'],
                        ['day' => 'Tuesday', 'enabled' => true, 'open' => '11:00', 'close' => '23:00'],
                        ['day' => 'Wednesday', 'enabled' => true, 'open' => '11:00', 'close' => '23:00'],
                        ['day' => 'Thursday', 'enabled' => true, 'open' => '11:00', 'close' => '23:00'],
                        ['day' => 'Friday', 'enabled' => true, 'open' => '11:00', 'close' => '24:00'],
                        ['day' => 'Saturday', 'enabled' => true, 'open' => '11:00', 'close' => '24:00'],
                        ['day' => 'Sunday', 'enabled' => true, 'open' => '11:00', 'close' => '22:00'],
                    ],
                    'always_open' => false
                ]
            ],
        ];

        foreach ($storeData as $index => $data) {
            $merchant = $merchants[$index] ?? $merchants[0];

            $store = Store::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'admin_id' => $merchant->id,
                    'is_active' => true,
                    'is_onboarding' => false,
                ])
            );
            $stores[] = $store;
        }

        $this->command->info('Created ' . count($stores) . ' stores');
        return $stores;
    }

    private function createCategories(array $stores): void
    {
        $categoryTemplates = [
            'Restaurant' => ['Appetizers', 'Main Courses', 'Rice & Noodles', 'Seafood', 'Vegetables', 'Desserts', 'Beverages'],
            'Cafe' => ['Coffee', 'Tea', 'Pastries', 'Sandwiches', 'Salads', 'Smoothies', 'Snacks'],
            'Fast Food' => ['Burgers', 'Fried Chicken', 'Fries', 'Wraps', 'Sides', 'Drinks', 'Desserts'],
            'Kitchen' => ['Rice Dishes', 'Curry', 'Stir Fry', 'Soup', 'Vegetables', 'Meat', 'Fish'],
            'Bistro' => ['Starters', 'Mains', 'Pasta', 'Pizza', 'Salads', 'Wine', 'Desserts'],
        ];

        foreach ($stores as $store) {
            $storeType = $this->getStoreType($store->name);
            $categories = $categoryTemplates[$storeType] ?? $categoryTemplates['Restaurant'];

            foreach ($categories as $categoryName) {
                Category::firstOrCreate(
                    ['name' => $categoryName, 'store_id' => $store->id],
                    ['is_active' => true]
                );
            }
        }

        $this->command->info('Created categories for all stores');
    }

    private function createMenuItems(array $stores): void
    {
        foreach ($stores as $store) {
            $categories = Category::where('store_id', $store->id)->get();

            foreach ($categories as $category) {
                $itemCount = fake()->numberBetween(3, 8);

                for ($i = 0; $i < $itemCount; $i++) {
                    $menuItem = $this->createMenuItem($store, $category);
                }
            }
        }

        $this->command->info('Created menu items for all stores');
    }

    private function createMenuItem(Store $store, Category $category): MenuItem
    {
        $basePrice = fake()->randomFloat(2, 5.00, 25.00);

        // Random options and addons
        $options = $this->generateRandomOptions();
        $addons = $this->generateRandomAddons();

        return MenuItem::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(8),
            'price' => $basePrice,
            'base_price' => $basePrice,
            'options' => $options,
            'addons' => $addons,
            'is_active' => fake()->boolean(90), // 90% active
            'stock' => fake()->numberBetween(0, 100),
            'image_path' => null, // Can be added later
        ]);
    }

    private function generateRandomOptions(): ?array
    {
        if (!fake()->boolean(60)) return null; // 40% chance of no options

        $optionGroups = [
            'Size' => [
                ['name' => 'Small', 'enabled' => true],
                ['name' => 'Medium', 'enabled' => true],
                ['name' => 'Large', 'enabled' => true],
            ],
            'Spice Level' => [
                ['name' => 'Mild', 'enabled' => true],
                ['name' => 'Medium', 'enabled' => true],
                ['name' => 'Hot', 'enabled' => true],
                ['name' => 'Extra Hot', 'enabled' => true],
            ],
            'Cooking Style' => [
                ['name' => 'Grilled', 'enabled' => true],
                ['name' => 'Fried', 'enabled' => true],
                ['name' => 'Steamed', 'enabled' => true],
                ['name' => 'Baked', 'enabled' => true],
            ],
            'Protein Choice' => [
                ['name' => 'Chicken', 'enabled' => true],
                ['name' => 'Beef', 'enabled' => true],
                ['name' => 'Fish', 'enabled' => true],
                ['name' => 'Vegetarian', 'enabled' => true],
            ],
        ];

        $selectedGroups = collect($optionGroups)->random(fake()->numberBetween(1, 2));
        $options = [];

        foreach ($selectedGroups as $groupName => $groupOptions) {
            $options[] = [
                'name' => $groupName,
                'enabled' => true,
                'rules' => fake()->randomElement([
                    ['required', 'one'],
                    ['required', 'multiple'],
                    ['optional', 'one'],
                    ['optional', 'multiple'],
                ]),
                'options' => $groupOptions,
            ];
        }

        return $options;
    }

    private function generateRandomAddons(): ?array
    {
        if (!fake()->boolean(50)) return null; // 50% chance of no addons

        $addonGroups = [
            'Extra Toppings' => [
                ['name' => 'Extra Cheese', 'enabled' => true, 'price' => fake()->randomFloat(2, 1.00, 3.00)],
                ['name' => 'Extra Meat', 'enabled' => true, 'price' => fake()->randomFloat(2, 2.00, 5.00)],
                ['name' => 'Extra Vegetables', 'enabled' => true, 'price' => fake()->randomFloat(2, 0.50, 2.00)],
            ],
            'Sides' => [
                ['name' => 'French Fries', 'enabled' => true, 'price' => fake()->randomFloat(2, 2.00, 4.00)],
                ['name' => 'Onion Rings', 'enabled' => true, 'price' => fake()->randomFloat(2, 2.50, 4.50)],
                ['name' => 'Side Salad', 'enabled' => true, 'price' => fake()->randomFloat(2, 3.00, 5.00)],
                ['name' => 'Soup of the Day', 'enabled' => true, 'price' => fake()->randomFloat(2, 4.00, 6.00)],
            ],
            'Beverages' => [
                ['name' => 'Soft Drink', 'enabled' => true, 'price' => fake()->randomFloat(2, 2.00, 3.00)],
                ['name' => 'Fresh Juice', 'enabled' => true, 'price' => fake()->randomFloat(2, 3.00, 5.00)],
                ['name' => 'Coffee', 'enabled' => true, 'price' => fake()->randomFloat(2, 2.50, 4.00)],
                ['name' => 'Tea', 'enabled' => true, 'price' => fake()->randomFloat(2, 2.00, 3.50)],
            ],
        ];

        $selectedGroups = collect($addonGroups)->random(fake()->numberBetween(1, 2));
        $addons = [];

        foreach ($selectedGroups as $groupName => $groupAddons) {
            $addons[] = [
                'name' => $groupName,
                'enabled' => true,
                'rules' => ['optional', 'multiple'],
                'options' => $groupAddons,
            ];
        }

        return $addons;
    }

    private function createCustomers(): array
    {
        $customers = [];

        for ($i = 0; $i < 25; $i++) {
            $customer = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);
            $customers[] = $customer;
        }

        $this->command->info('Created ' . count($customers) . ' customers');
        return $customers;
    }

    private function createAddresses(array $customers): void
    {
        foreach ($customers as $customer) {
            $addressCount = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $addressCount; $i++) {
                UserAddress::create([
                    'user_id' => $customer->id,
                    'label' => $i === 0 ? 'Home' : ($i === 1 ? 'Office' : 'Other'),
                    'recipient_name' => $customer->name,
                    'phone' => fake()->numerify('01#-########'),
                    'line1' => fake()->streetAddress(),
                    'line2' => fake()->optional()->secondaryAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'postal_code' => fake()->postcode(),
                    'country' => 'MY',
                    'is_default' => $i === 0,
                ]);
            }
        }

        $this->command->info('Created addresses for all customers');
    }

    private function createOrders(array $stores, array $customers): void
    {
        foreach ($stores as $store) {
            $menuItems = MenuItem::where('store_id', $store->id)->get();
            $orderCount = fake()->numberBetween(15, 30);

            for ($i = 0; $i < $orderCount; $i++) {
                $this->createOrder($store, $customers, $menuItems);
            }
        }

        $this->command->info('Created orders for all stores');
    }

    private function createOrder(Store $store, array $customers, $menuItems): void
    {
        $customer = $customers[array_rand($customers)];
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

        // Create order items
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

        // Update order totals
        $tax = $subtotal * 0.08; // 8% tax
        $deliveryFee = $order->delivery_fee ?? 0;
        $total = $subtotal + $tax + $deliveryFee;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    private function createCartItems(array $stores, array $customers): void
    {
        foreach ($customers as $customer) {
            $store = $stores[array_rand($stores)];
            $menuItems = MenuItem::where('store_id', $store->id)->get();

            if ($menuItems->isEmpty()) continue;

            $cartItemCount = fake()->numberBetween(0, 5);

            for ($i = 0; $i < $cartItemCount; $i++) {
                $menuItem = $menuItems->random();
                $qty = fake()->numberBetween(1, 3);

                [$selections, $addonsTotal] = $this->buildSelectionsFromMenuItem($menuItem);

                CartItem::create([
                    'user_id' => $customer->id,
                    'menu_item_id' => $menuItem->id,
                    'qty' => $qty,
                    'unit_price' => (float) $menuItem->price + $addonsTotal,
                    'selections' => $selections,
                ]);
            }
        }

        $this->command->info('Created cart items for customers');
    }

    private function buildSelectionsFromMenuItem(MenuItem $menuItem): array
    {
        $selections = [
            'options' => [],
            'addons' => [],
        ];

        $addonsTotal = 0.0;

        // Process options
        $optionGroups = $menuItem->options ?? [];
        foreach ($optionGroups as $group) {
            if (empty($group['options'])) continue;

            $rules = $group['rules'] ?? [];
            $chooseMultiple = in_array('multiple', $rules);
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

        // Process addons
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
        }

        return [$selections, $addonsTotal];
    }

    private function getStoreType(string $storeName): string
    {
        if (str_contains(strtolower($storeName), 'cafe')) return 'Cafe';
        if (str_contains(strtolower($storeName), 'fast') || str_contains(strtolower($storeName), 'food')) return 'Fast Food';
        if (str_contains(strtolower($storeName), 'kitchen')) return 'Kitchen';
        if (str_contains(strtolower($storeName), 'bistro')) return 'Bistro';
        return 'Restaurant';
    }
}
