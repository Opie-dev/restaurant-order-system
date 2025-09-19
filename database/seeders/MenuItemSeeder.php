<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        //clear data
        // DB::table('menu_items')->truncate();
        // DB::table('categories')->truncate();
        // Storage::disk('public')->deleteDirectory('menu');

        // Ensure storage/app/public/menu exists (served via public/storage/menu after storage:link)
        Storage::disk('public')->makeDirectory('menu');
        $optionGroups = [
            'ikan' => [
                ['name' => 'Talapia Merah Bakar', 'enabled' => true],
                ['name' => 'Cencaru Bakar', 'enabled' => true],
                ['name' => 'Kembung Bakar', 'enabled' => true],
                ['name' => 'Pari Bakar', 'enabled' => true],
            ],
            'sayur' => [
                ['name' => 'Terung', 'enabled' => true],
                ['name' => 'Kobis', 'enabled' => true],
                ['name' => 'Kangkung', 'enabled' => true],
                ['name' => 'Bendi', 'enabled' => true],
                ['name' => 'Ulam', 'enabled' => true],
                ['name' => 'Tauge', 'enabled' => true],
                ['name' => 'Peria', 'enabled' => true],
                ['name' => 'Kacang Panjang', 'enabled' => true],
            ],
            'side' => [
                ['name' => 'Bergedil', 'enabled' => true],
                ['name' => 'Tempe Goreng', 'enabled' => true],
                ['name' => 'Tauhu Goreng', 'enabled' => true],
                ['name' => 'Air Asam', 'enabled' => true],
                ['name' => 'Air Asam Bekas', 'enabled' => true],
                ['name' => 'Sambal Belacan', 'enabled' => true],
                ['name' => 'Telor Dadar', 'enabled' => true],
                ['name' => 'Nasi Putih', 'enabled' => true],
            ],
            'ayam_bahagian' => [
                ['name' => 'Peha / Thigh', 'enabled' => true],
                ['name' => 'Kepak / Wing', 'enabled' => true],
                ['name' => 'Dada / Breast', 'enabled' => true],
            ],
            'ayam_jenis' => [
                ['name' => 'Ayam Goreng', 'enabled' => true],
                ['name' => 'Ayam Kari', 'enabled' => true],
            ],
        ];

        $menu = [
            //set
            [
                'name' => 'Set Nasi Ikan Bakar',
                'description' => 'Set lengkap dengan nasi putih, ikan bakar dan pilihan sayur.',
                'enabled' => true,
                'base_price' => 10.00,
                'type' => 'set', // or ala_carte
                'addons' => [
                    [
                        'name' => 'Pilih Ikan',
                        'enabled' => true,
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['ikan']
                    ],
                    [
                        'name' => 'Pilih Sayur',
                        'enabled' => true,
                        'rules' => ['required', 'multiple'],
                        'options' => $optionGroups['sayur']
                    ],
                    [
                        'name' => 'AddOns Side',
                        'enabled' => true,
                        'rules' => ['optional', 'multiple'],
                        'options' => $optionGroups['side']
                    ]
                ]
            ],
            [
                'name' => 'Set Nasi Ayam',
                'description' => 'Set lengkap dengan nasi putih, ikan bakar dan pilihan sayur.',
                'base_price' => 10.00,
                'type' => 'set', // or ala_carte
                'addons' => [
                    [
                        'name' => 'Pilih Jenis Ayam',
                        'enabled' => true,
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['ayam_jenis']
                    ],
                    [
                        'name' => 'Pilih Sayur',
                        'enabled' => true,
                        'rules' => ['required', 'multiple'],
                        'options' => $optionGroups['sayur']
                    ],
                    [
                        'name' => 'AddOns Side',
                        'enabled' => true,
                        'rules' => ['optional', 'multiple'],
                        'options' => $optionGroups['side']
                    ]
                ]
            ],
            //ala carte
            [
                'name' => 'Ayam',
                'description' => 'Ayam pilihan',
                'enabled' => true,
                'type' => 'ala_carte',
                'options' => [
                    [
                        'name' => 'Choose Type',
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['ayam_jenis']
                    ],
                    [
                        'name' => 'Choose Part',
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['ayam_bahagian']
                    ]
                ],
            ],
            [
                'name' => 'Ikan',
                'description' => 'Ikan pilihan',
                'enabled' => true,
                'type' => 'ala_carte',
                'options' => [
                    [
                        'name' => 'Choose Fish',
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['ikan']
                    ]
                ]
            ],
            [
                'name' => 'Sayur',
                'description' => 'Sayur pilihan',
                'enabled' => true,
                'type' => 'ala_carte',
                'options' => [
                    [
                        'name' => 'Choose Vegetables',
                        'rules' => ['required', 'multiple'],
                        'options' => $optionGroups['sayur']
                    ]
                ]
            ],
            [
                'name' => 'Side',
                'description' => 'Side pilihan',
                'enabled' => true,
                'type' => 'ala_carte',
                'options' => [
                    [
                        'name' => 'Choose Sides',
                        'rules' => ['optional', 'multiple'],
                        'options' => $optionGroups['side']
                    ]
                ]
            ],

        ];

        // Create a default category for the new items
        $category = Category::firstOrCreate(
            ['name' => 'Ikan Bakar Temoh'],
            ['is_active' => true]
        );

        foreach ($menu as $itemData) {
            $item = MenuItem::firstOrCreate(
                ['name' => $itemData['name']],
                [
                    'category_id' => $category->id,
                    'description' => $itemData['description'] ?? '',
                    'price' => $itemData['base_price'] ?? 0,
                    'base_price' => $itemData['base_price'] ?? null,
                    'type' => $itemData['type'] ?? 'ala_carte',
                    'options' => $itemData['options'] ?? null,
                    'addons' => $itemData['addons'] ?? null,
                    'is_active' => $itemData['enabled'] ?? true,
                    'enabled' => $itemData['enabled'] ?? true,
                    'stock' => 100, // Default stock
                ]
            );
        }
    }
}
