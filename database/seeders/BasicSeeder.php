<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BasicSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin and customer for testing
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        if (!User::where('email', 'customer@example.com')->exists()) {
            User::create([
                'name' => 'Customer',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);
        }

        // Run basic seeders for minimal dataset
        $this->call([
            StoreSeeder::class,
            MenuItemSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            UserAddressSeeder::class,
        ]);
    }
}
