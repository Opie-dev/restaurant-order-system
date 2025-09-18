<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 5, 25);
        $qty = fake()->numberBetween(1, 5);
        $lineTotal = $unitPrice * $qty;

        return [
            'order_id' => \App\Models\Order::factory(),
            'menu_item_id' => \App\Models\MenuItem::factory(),
            'name_snapshot' => fake()->words(2, true),
            'unit_price' => $unitPrice,
            'qty' => $qty,
            'line_total' => $lineTotal,
        ];
    }
}
