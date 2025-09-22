<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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

        $this->call([
            MenuItemSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            UserAddressSeeder::class,
        ]);
    }
}
