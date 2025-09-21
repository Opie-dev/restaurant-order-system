<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\User;
use App\Models\Category;
use Tests\TestCase;

class EnhancedOrderDisplayTest extends TestCase
{
    public function test_order_items_display_with_selections(): void
    {
        // Create test data with unique identifiers to avoid conflicts
        $testUserId = 999999;
        $testMenuItemId = 999999;
        $testCategoryId = 999999;

        // Create test category
        $category = Category::firstOrCreate(
            ['id' => $testCategoryId],
            [
                'name' => 'Test Category Display',
                'is_active' => true
            ]
        );

        // Create test user (only if doesn't exist)
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User for Display',
                'email' => 'test-display@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        // Create test menu item (only if doesn't exist)
        $menuItem = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId],
            [
                'category_id' => $category->id,
                'name' => 'Test Burger for Display',
                'description' => 'Test burger for display testing',
                'price' => 15.00,
                'is_active' => true
            ]
        );

        // Create test order
        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-DISPLAY-' . time(),
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
                'addons' => ['Extra Cheese', 'Bacon', 'Extra Sauce']
            ]
        ]);

        // Test that the order item displays all information correctly
        $this->assertTrue($orderItem->hasSelections());
        $this->assertEquals(['Medium', 'Extra Spicy'], $orderItem->getSelectionsByType('options'));
        $this->assertEquals(['Extra Cheese', 'Bacon', 'Extra Sauce'], $orderItem->getSelectionsByType('addons'));

        $formatted = $orderItem->getFormattedSelections();
        $this->assertStringContainsString('Options: Medium, Extra Spicy', $formatted);
        $this->assertStringContainsString('Addons: Extra Cheese, Bacon, Extra Sauce', $formatted);

        // Test that the order loads with items
        $order->load('items');
        $this->assertCount(1, $order->items);
        $this->assertEquals($orderItem->id, $order->items->first()->id);

        // Clean up test data
        $orderItem->delete();
        $order->delete();
    }

    public function test_order_with_multiple_items_and_selections(): void
    {
        $testUserId = 999998;
        $testMenuItemId1 = 999998;
        $testMenuItemId2 = 999997;
        $testCategoryId1 = 999998;
        $testCategoryId2 = 999997;

        // Create test categories
        $category1 = Category::firstOrCreate(
            ['id' => $testCategoryId1],
            [
                'name' => 'Test Category Multiple 1',
                'is_active' => true
            ]
        );

        $category2 = Category::firstOrCreate(
            ['id' => $testCategoryId2],
            [
                'name' => 'Test Category Multiple 2',
                'is_active' => true
            ]
        );

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Multiple Items',
                'email' => 'test-multiple@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        // Create test menu items
        $menuItem1 = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId1],
            [
                'category_id' => $category1->id,
                'name' => 'Test Burger Multiple',
                'description' => 'Test burger for multiple items testing',
                'price' => 15.00,
                'is_active' => true
            ]
        );

        $menuItem2 = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId2],
            [
                'category_id' => $category2->id,
                'name' => 'Test Fries Multiple',
                'description' => 'Test fries for multiple items testing',
                'price' => 5.00,
                'is_active' => true
            ]
        );

        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-MULTIPLE-' . time(),
            'status' => Order::STATUS_CONFIRMED,
            'subtotal' => 20.00,
            'tax' => 2.00,
            'total' => 22.00,
            'payment_status' => 'paid'
        ]);

        // Create first item with selections
        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem1->id,
            'name_snapshot' => $menuItem1->name,
            'qty' => 1,
            'unit_price' => 15.00,
            'line_total' => 15.00,
            'selections' => [
                'options' => ['Large'],
                'addons' => ['Extra Cheese']
            ]
        ]);

        // Create second item without selections
        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem2->id,
            'name_snapshot' => $menuItem2->name,
            'qty' => 1,
            'unit_price' => 5.00,
            'line_total' => 5.00,
            'selections' => null
        ]);

        $order->load('items');

        $this->assertCount(2, $order->items);

        $burgerItem = $order->items->where('name_snapshot', 'Test Burger Multiple')->first();
        $friesItem = $order->items->where('name_snapshot', 'Test Fries Multiple')->first();

        $this->assertTrue($burgerItem->hasSelections());
        $this->assertFalse($friesItem->hasSelections());

        $this->assertEquals(['Large'], $burgerItem->getSelectionsByType('options'));
        $this->assertEquals(['Extra Cheese'], $burgerItem->getSelectionsByType('addons'));

        // Clean up test data
        $order->items()->delete();
        $order->delete();
    }

    public function test_order_status_and_selections_display(): void
    {
        $testUserId = 999996;
        $testMenuItemId = 999996;
        $testCategoryId = 999996;

        // Create test category
        $category = Category::firstOrCreate(
            ['id' => $testCategoryId],
            [
                'name' => 'Test Category Status',
                'is_active' => true
            ]
        );

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Status',
                'email' => 'test-status@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]
        );

        // Create test menu item
        $menuItem = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId],
            [
                'category_id' => $category->id,
                'name' => 'Test Pizza Status',
                'description' => 'Test pizza for status testing',
                'price' => 20.00,
                'is_active' => true
            ]
        );

        $order = Order::create([
            'user_id' => $user->id,
            'code' => 'TEST-STATUS-' . time(),
            'status' => Order::STATUS_PREPARING,
            'subtotal' => 20.00,
            'tax' => 2.00,
            'total' => 22.00,
            'payment_status' => 'paid'
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'name_snapshot' => $menuItem->name,
            'qty' => 1,
            'unit_price' => 20.00,
            'line_total' => 20.00,
            'selections' => [
                'options' => ['Thin Crust', 'Extra Cheese'],
                'addons' => ['Pepperoni', 'Mushrooms', 'Olives']
            ]
        ]);

        // Test order status methods
        $this->assertTrue($order->isPending());
        $this->assertFalse($order->isCompleted());
        $this->assertTrue($order->canTransitionTo(Order::STATUS_READY));
        $this->assertTrue($order->canTransitionTo(Order::STATUS_CANCELLED));

        // Test selections display
        $this->assertTrue($orderItem->hasSelections());
        $selections = $orderItem->getSelectionsArray();
        $this->assertArrayHasKey('options', $selections);
        $this->assertArrayHasKey('addons', $selections);
        $this->assertCount(2, $selections['options']);
        $this->assertCount(3, $selections['addons']);

        // Clean up test data
        $orderItem->delete();
        $order->delete();
    }
}
