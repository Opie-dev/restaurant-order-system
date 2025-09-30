<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create an admin user
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        // Create a default store with granular address fields
        $store = Store::firstOrCreate(
            ['slug' => 'main-restaurant'],
            [
                'name' => 'Main Restaurant',
                'description' => 'Our main restaurant location',
                'address_line1' => '123 Main Street',
                'address_line2' => null,
                'city' => 'Sample City',
                'state' => 'Sample State',
                'postal_code' => '12345',
                'phone' => '+1 (555) 123-4567',
                'email' => 'info@mainrestaurant.com',
                'admin_id' => $admin->id,
                'is_active' => true,
            ]
        );

        // Update existing categories to belong to this store
        Category::whereNull('store_id')->update(['store_id' => $store->id]);

        // Update existing menu items to belong to this store
        MenuItem::whereNull('store_id')->update(['store_id' => $store->id]);

        // Update existing orders to belong to this store
        Order::whereNull('store_id')->update(['store_id' => $store->id]);

        $this->command->info('Store created and existing data updated successfully!');
    }
}
