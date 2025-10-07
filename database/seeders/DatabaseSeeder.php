<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Organized seeding pipeline
        $this->call([
            MerchantSeeder::class,
            StoresComprehensiveSeeder::class,
            CategoriesComprehensiveSeeder::class,
            MenuItemsComprehensiveSeeder::class,
            CustomersSeeder::class,
            OrdersComprehensiveSeeder::class,
            CartsComprehensiveSeeder::class,
        ]);
    }
}
