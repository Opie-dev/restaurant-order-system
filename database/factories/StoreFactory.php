<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'admin']);
        }

        return [
            'name' => $name,
            'slug' => Str::slug($name . '-' . Str::random(4)),
            'description' => $this->faker->optional()->sentence(8),
            'address_line1' => $this->faker->streetAddress(),
            'address_line2' => $this->faker->optional()->secondaryAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'phone' => $this->faker->e164PhoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'logo_path' => null,
            'settings' => [],
            'is_active' => true,
            'admin_id' => $admin->id,
        ];
    }
}
