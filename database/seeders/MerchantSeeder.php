<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $merchantData = [
            ['name' => 'Ahmad Restaurant', 'email' => 'ahmad@restaurant.com'],
            ['name' => 'Sarah Cafe', 'email' => 'sarah@cafe.com'],
            ['name' => 'Hassan Food', 'email' => 'hassan@food.com'],
            ['name' => 'Fatima Kitchen', 'email' => 'fatima@kitchen.com'],
            ['name' => 'Omar Bistro', 'email' => 'omar@bistro.com'],
            ['name' => 'Admin', 'email' => 'admin@example.com'],
        ];

        foreach ($merchantData as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                ]
            );
        }

        $this->command?->info('MerchantSeeder: merchants ensured.');
    }
}
