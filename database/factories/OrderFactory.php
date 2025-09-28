<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 10, 100);
        $tax = $subtotal * 0.1; // 10% tax
        $deliveryFee = fake()->boolean(70) ? fake()->randomFloat(2, 3.00, 8.00) : null; // 70% chance of delivery
        $total = $subtotal + $tax + ($deliveryFee ?? 0);

        return [
            'user_id' => \App\Models\User::factory(),
            'code' => strtoupper(fake()->unique()->lexify('??????')),
            'status' => fake()->randomElement(['pending', 'preparing', 'delivering', 'completed', 'cancelled']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'payment_status' => fake()->randomElement(['unpaid', 'processing', 'paid', 'refunded', 'failed']),
            'payment_provider' => fake()->randomElement(['stripe', 'paypal', null]),
            'payment_ref' => fake()->optional()->uuid(),
            'tracking_url' => fake()->optional()->url(),
            'delivery_fee' => $deliveryFee,
            'notes' => fake()->optional()->sentence(),
            'cancellation_remarks' => fake()->optional()->sentence(),
        ];
    }
}
