<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_order_status_transitions(): void
    {
        $testUserId = 999992;

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Status Transitions',
                'email' => 'test-statustransitions@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-STATUSTRANSITIONS-' . time(),
            'status' => Order::STATUS_PENDING,
            'subtotal' => 15.00,
            'tax' => 1.50,
            'total' => 16.50,
            'payment_status' => 'paid'
        ]);

        // Test valid transitions
        $this->assertTrue($order->canTransitionTo(Order::STATUS_CONFIRMED));
        $this->assertTrue($order->canTransitionTo(Order::STATUS_CANCELLED));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_PREPARING));

        // Test status update
        $order->update(['status' => Order::STATUS_CONFIRMED]);
        $this->assertEquals(Order::STATUS_CONFIRMED, $order->status);

        // Test next valid transitions
        $this->assertTrue($order->canTransitionTo(Order::STATUS_PREPARING));
        $this->assertTrue($order->canTransitionTo(Order::STATUS_CANCELLED));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_READY));

        // Clean up test data
        $order->delete();
    }

    public function test_order_status_helper_methods(): void
    {
        $testUserId = 999991;

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Helper Methods',
                'email' => 'test-helpermethods@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        $pendingOrder = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-PENDING-' . time(),
            'status' => Order::STATUS_PENDING,
            'subtotal' => 15.00,
            'tax' => 1.50,
            'total' => 16.50,
            'payment_status' => 'paid'
        ]);

        $completedOrder = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-COMPLETED-' . time(),
            'status' => Order::STATUS_COMPLETED,
            'subtotal' => 15.00,
            'tax' => 1.50,
            'total' => 16.50,
            'payment_status' => 'paid'
        ]);

        $this->assertTrue($pendingOrder->isPending());
        $this->assertFalse($pendingOrder->isCompleted());

        $this->assertFalse($completedOrder->isPending());
        $this->assertTrue($completedOrder->isCompleted());

        // Clean up test data
        $pendingOrder->delete();
        $completedOrder->delete();
    }

    public function test_order_status_color_classes(): void
    {
        $testUserId = 999990;

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Color Classes',
                'email' => 'test-colorclasses@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        $pendingOrder = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-PENDING-COLOR-' . time(),
            'status' => Order::STATUS_PENDING,
            'subtotal' => 15.00,
            'tax' => 1.50,
            'total' => 16.50,
            'payment_status' => 'paid'
        ]);

        $completedOrder = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-COMPLETED-COLOR-' . time(),
            'status' => Order::STATUS_COMPLETED,
            'subtotal' => 15.00,
            'tax' => 1.50,
            'total' => 16.50,
            'payment_status' => 'paid'
        ]);

        $this->assertStringContainsString('yellow', $pendingOrder->getStatusColorClass());
        $this->assertStringContainsString('green', $completedOrder->getStatusColorClass());

        // Clean up test data
        $pendingOrder->delete();
        $completedOrder->delete();
    }
}
