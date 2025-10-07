<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoresComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = User::where('role', 'admin')->orderBy('id')->get();
        if ($merchants->isEmpty()) {
            $this->call(MerchantSeeder::class);
            $merchants = User::where('role', 'admin')->orderBy('id')->get();
        }

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
            $merchant = $merchants[$index] ?? $merchants->first();
            Store::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'admin_id' => $merchant->id,
                    'is_active' => true,
                    'is_onboarding' => false,
                ])
            );
        }

        $this->command?->info('StoresComprehensiveSeeder: stores ensured.');
    }
}
