<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        $count = 25;
        for ($i = 0; $i < $count; $i++) {
            $customer = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);

            $addrCount = fake()->numberBetween(1, 3);
            for ($j = 0; $j < $addrCount; $j++) {
                UserAddress::create([
                    'user_id' => $customer->id,
                    'label' => $j === 0 ? 'Home' : ($j === 1 ? 'Office' : 'Other'),
                    'recipient_name' => $customer->name,
                    'phone' => fake()->numerify('01#-########'),
                    'line1' => fake()->streetAddress(),
                    'line2' => fake()->optional()->secondaryAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'postal_code' => fake()->postcode(),
                    'country' => 'MY',
                    'is_default' => $j === 0,
                ]);
            }
        }

        $this->command?->info("CustomersSeeder: {$count} customers created with addresses.");
    }
}
