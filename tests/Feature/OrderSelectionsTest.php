<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\User;
use App\Models\Category;
use Tests\TestCase;

class OrderSelectionsTest extends TestCase
{
    public function test_order_item_displays_selections(): void
    {
        $testUserId = 999995;
        $testMenuItemId = 999995;
        $testCategoryId = 999995;

        // Create test category
        $category = Category::firstOrCreate(
            ['id' => $testCategoryId],
            [
                'name' => 'Test Category Selections',
                'is_active' => true
            ]
        );

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Selections',
                'email' => 'test-selections@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        // Create test menu item
        $menuItem = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId],
            [
                'category_id' => $category->id,
                'name' => 'Test Burger Selections',
                'description' => 'Test burger for selections testing',
                'price' => 15.00,
                'is_active' => true
            ]
        );

        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-SELECTIONS-' . time(),
            'status' => Order::STATUS_PENDING,
            'subtotal' => 30.00,
            'tax' => 3.00,
            'total' => 33.00,
            'payment_status' => 'paid'
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'name_snapshot' => $menuItem->name,
            'qty' => 2,
            'unit_price' => 15.00,
            'line_total' => 30.00,
            'selections' => [
                'options' => ['Medium', 'Extra Spicy'],
                'addons' => ['Extra Cheese', 'Bacon']
            ]
        ]);

        // Test helper methods
        $this->assertTrue($orderItem->hasSelections());
        $this->assertEquals(['Medium', 'Extra Spicy'], $orderItem->getSelectionsByType('options'));
        $this->assertEquals(['Extra Cheese', 'Bacon'], $orderItem->getSelectionsByType('addons'));

        $formatted = $orderItem->getFormattedSelections();
        $this->assertStringContainsString('Options: Medium, Extra Spicy', $formatted);
        $this->assertStringContainsString('Addons: Extra Cheese, Bacon', $formatted);

        // Clean up test data
        $orderItem->delete();
        $order->delete();
    }

    public function test_order_item_without_selections(): void
    {
        $testUserId = 999994;
        $testMenuItemId = 999994;
        $testCategoryId = 999994;

        // Create test category
        $category = Category::firstOrCreate(
            ['id' => $testCategoryId],
            [
                'name' => 'Test Category No Selections',
                'is_active' => true
            ]
        );

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User No Selections',
                'email' => 'test-noselections@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        // Create test menu item
        $menuItem = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId],
            [
                'category_id' => $category->id,
                'name' => 'Test Burger No Selections',
                'description' => 'Test burger without selections',
                'price' => 15.00,
                'is_active' => true
            ]
        );

        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-NOSELECTIONS-' . time(),
            'status' => Order::STATUS_PENDING,
            'subtotal' => 15.00,
            'tax' => 1.50,
            'total' => 16.50,
            'payment_status' => 'paid'
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'name_snapshot' => $menuItem->name,
            'qty' => 1,
            'unit_price' => 15.00,
            'line_total' => 15.00,
            'selections' => null
        ]);

        $this->assertFalse($orderItem->hasSelections());
        $this->assertEquals('', $orderItem->getFormattedSelections());
        $this->assertEquals([], $orderItem->getSelectionsArray());

        // Clean up test data
        $orderItem->delete();
        $order->delete();
    }

    public function test_order_item_with_empty_selections(): void
    {
        $testUserId = 999993;
        $testMenuItemId = 999993;
        $testCategoryId = 999993;

        // Create test category
        $category = Category::firstOrCreate(
            ['id' => $testCategoryId],
            [
                'name' => 'Test Category Empty Selections',
                'is_active' => true
            ]
        );

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Empty Selections',
                'email' => 'test-empty@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        // Create test menu item
        $menuItem = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId],
            [
                'category_id' => $category->id,
                'name' => 'Test Burger Empty Selections',
                'description' => 'Test burger with empty selections',
                'price' => 15.00,
                'is_active' => true
            ]
        );

        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-EMPTY-' . time(),
            'status' => Order::STATUS_PENDING,
            'subtotal' => 15.00,
            'tax' => 1.50,
            'total' => 16.50,
            'payment_status' => 'paid'
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'name_snapshot' => $menuItem->name,
            'qty' => 1,
            'unit_price' => 15.00,
            'line_total' => 15.00,
            'selections' => []
        ]);

        $this->assertFalse($orderItem->hasSelections());
        $this->assertEquals('', $orderItem->getFormattedSelections());

        // Clean up test data
        $orderItem->delete();
        $order->delete();
    }
}
