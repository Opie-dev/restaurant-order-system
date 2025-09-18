<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command?->warn('No users found. Skipping UserAddressSeeder.');
            return;
        }

        foreach ($users as $user) {
            // Clear existing for idempotency in dev
            // UserAddress::where('user_id', $user->id)->delete();

            $count = rand(1, 3);
            $defaultIndex = rand(1, $count);

            for ($i = 1; $i <= $count; $i++) {
                UserAddress::create([
                    'user_id' => $user->id,
                    'label' => $i === 1 ? 'Home' : ($i === 2 ? 'Office' : 'Other'),
                    'recipient_name' => $user->name,
                    'phone' => fake()->numerify('01#-########'),
                    'line1' => fake()->streetAddress(),
                    'line2' => fake()->optional()->secondaryAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'postal_code' => fake()->postcode(),
                    'country' => 'MY',
                    'is_default' => $i === $defaultIndex,
                ]);
            }
        }

        $this->command?->info('Seeded user addresses.');
    }
}
