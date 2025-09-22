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
            'ayam_bahagian' => [
                ['name' => 'Peha / Thigh', 'enabled' => true],
                ['name' => 'Kepak / Wing', 'enabled' => true],
                ['name' => 'Dada / Breast', 'enabled' => true],
            ],
            'ayam_jenis' => [
                ['name' => 'Ayam Goreng', 'enabled' => true],
                ['name' => 'Ayam Kari', 'enabled' => true],
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
        ];

        $addons = [
            'sayur' => [
                ['name' => 'Terung', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Kobis', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Kangkung', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Bendi', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Ulam', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Tauge', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Peria', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Kacang Panjang', 'enabled' => true, 'price' => 1.00],
            ],
            'side' => [
                ['name' => 'Bergedil', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Tempe Goreng', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Tauhu Goreng', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Air Asam', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Air Asam Bekas', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Sambal Belacan', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Telor Dadar', 'enabled' => true, 'price' => 1.00],
                ['name' => 'Nasi Putih', 'enabled' => true, 'price' => 1.00],
            ],
        ];

        $categories = [
            'Set',
            'Ayam',
            'Ikan Bakar',
            'Sayur',
            'Side',
        ];

        foreach ($categories as $category) {
            $category = Category::firstOrCreate(
                ['name' => $category],
                ['is_active' => true]
            );
        }

        $menu = [
            //set
            [
                'category' => 'Set',
                'name' => 'Set Nasi Ikan Bakar',
                'description' => 'Set lengkap dengan nasi putih, ikan bakar dan pilihan sayur.',
                'enabled' => true,
                'base_price' => 10.00,
                'type' => 'set', // or ala_carte
                'options' => [
                    [
                        'name' => 'Pilih Ikan',
                        'enabled' => true,
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['ikan'],
                    ],
                    [
                        'name' => 'Pilih Sayur',
                        'enabled' => true,
                        'rules' => ['required', 'multiple'],
                        'options' => $optionGroups['sayur'],
                    ],
                ],
                'addons' => [
                    [
                        'name' => 'Addons Side',
                        'enabled' => true,
                        'rules' => ['optional', 'multiple'],
                        'options' => $addons['side']
                    ],
                ]
            ],
            [
                'category' => 'Set',
                'name' => 'Set Nasi Ayam',
                'description' => 'Set lengkap dengan nasi putih, ikan bakar dan pilihan sayur.',
                'base_price' => 10.00,
                'type' => 'set', // or ala_carte
                'options' => [
                    [
                        'name' => 'Pilih Bahagian Ayam',
                        'enabled' => true,
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['ayam_bahagian']
                    ],
                    [
                        'name' => 'Pilih Sayur',
                        'enabled' => true,
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['sayur']
                    ],
                ],
                'addons' => [
                    [
                        'name' => 'Pilih Side',
                        'enabled' => true,
                        'rules' => ['optional', 'multiple'],
                        'options' => $addons['side']
                    ]
                ]
            ],
            //ala carte
            [
                'category' => 'Ayam',
                'name' => 'Ayam',
                'description' => 'Ayam pilihan',
                'enabled' => true,
                'type' => 'ala_carte',
                'base_price' => 8.00,
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
                'category' => 'Ikan Bakar',
                'name' => 'Ikan',
                'description' => 'Ikan bakar pilihan',
                'enabled' => true,
                'type' => 'ala_carte',
                'base_price' => 12.00,
                'options' => [
                    [
                        'name' => 'Pilih Ikan',
                        'rules' => ['required', 'one'],
                        'options' => $optionGroups['ikan']
                    ]
                ]
            ],
            [
                'category' => 'Sayur',
                'name' => 'Sayur',
                'description' => 'Sayur pilihan',
                'enabled' => true,
                'type' => 'ala_carte',
                'base_price' => 4.00,
                'options' => [
                    [
                        'name' => 'Choose Vegetables',
                        'rules' => ['required', 'multiple'],
                        // use priced version so addons pricing can be surfaced if needed later
                        'options' => $addons['sayur']
                    ]
                ]
            ],
            [
                'category' => 'Side',
                'name' => 'Side',
                'description' => 'Side pilihan',
                'enabled' => true,
                'type' => 'ala_carte',
                'base_price' => 2.00,
                'options' => [
                    [
                        'name' => 'Pilih Side',
                        'rules' => ['optional', 'multiple'],
                        // use priced side entries so UI can show (+price)
                        'options' => $addons['side']
                    ]
                ]
            ],

        ];

        foreach ($menu as $itemData) {
            $item = MenuItem::firstOrCreate(
                ['name' => $itemData['name']],
                [
                    'category_id' => Category::where('name', $itemData['category'])->first()->id,
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
