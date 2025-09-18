<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $seed = Str::slug($name . '-' . $this->faker->uuid());

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'description' => $this->faker->sentence(12),
            'price' => $this->faker->randomFloat(2, 1, 50),
            // Use a remote placeholder image URL
            'image_path' => "https://picsum.photos/seed/{$seed}/640/360",
            'is_active' => $this->faker->boolean(90),
            'position' => 0,
        ];
    }
}
