<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Category;
use App\Models\User;
use Tests\TestCase;

class AdminMenuEditOptionGroupDisableTest extends TestCase
{
    public function test_option_group_disable_preview_only(): void
    {
        $testUserId = 999980;
        $testMenuItemId = 999980;
        $testCategoryId = 999980;

        // Create test category
        $category = Category::firstOrCreate(
            ['id' => $testCategoryId],
            [
                'name' => 'Test Category Option Disable',
                'is_active' => true
            ]
        );

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Option Disable',
                'email' => 'test-optiondisable@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );

        // Create test menu item with options
        $menuItem = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId],
            [
                'category_id' => $category->id,
                'name' => 'Test Burger Option Disable',
                'description' => 'Test burger for option disable testing',
                'price' => 15.00,
                'is_active' => true,
                'options' => [
                    [
                        'name' => 'Size',
                        'enabled' => true,
                        'rules' => ['required', 'one'],
                        'options' => [
                            ['name' => 'Small', 'enabled' => true],
                            ['name' => 'Medium', 'enabled' => true],
                            ['name' => 'Large', 'enabled' => true]
                        ]
                    ],
                    [
                        'name' => 'Spice Level',
                        'enabled' => true,
                        'rules' => ['optional', 'one'],
                        'options' => [
                            ['name' => 'Mild', 'enabled' => true],
                            ['name' => 'Medium', 'enabled' => true],
                            ['name' => 'Hot', 'enabled' => true]
                        ]
                    ]
                ],
                'addons' => [
                    [
                        'name' => 'Extra Toppings',
                        'enabled' => true,
                        'rules' => ['optional', 'multiple'],
                        'options' => [
                            ['name' => 'Extra Cheese', 'price' => 2.00, 'enabled' => true],
                            ['name' => 'Bacon', 'price' => 3.00, 'enabled' => true],
                            ['name' => 'Avocado', 'price' => 1.50, 'enabled' => true]
                        ]
                    ]
                ]
            ]
        );

        // Test initial state - all groups and options should be enabled
        $this->assertTrue($menuItem->options[0]['enabled']);
        $this->assertTrue($menuItem->options[0]['options'][0]['enabled']);
        $this->assertTrue($menuItem->options[0]['options'][1]['enabled']);
        $this->assertTrue($menuItem->options[0]['options'][2]['enabled']);

        $this->assertTrue($menuItem->options[1]['enabled']);
        $this->assertTrue($menuItem->options[1]['options'][0]['enabled']);
        $this->assertTrue($menuItem->options[1]['options'][1]['enabled']);
        $this->assertTrue($menuItem->options[1]['options'][2]['enabled']);

        $this->assertTrue($menuItem->addons[0]['enabled']);
        $this->assertTrue($menuItem->addons[0]['options'][0]['enabled']);
        $this->assertTrue($menuItem->addons[0]['options'][1]['enabled']);
        $this->assertTrue($menuItem->addons[0]['options'][2]['enabled']);

        // Simulate disabling the first option group (only the group, not individual options)
        $menuItem->options[0]['enabled'] = false;
        $menuItem->save();

        // Reload the menu item
        $menuItem->refresh();

        // Test that the group is disabled
        $this->assertFalse($menuItem->options[0]['enabled']);

        // Test that individual options remain enabled (can still be edited)
        $this->assertTrue($menuItem->options[0]['options'][0]['enabled']);
        $this->assertTrue($menuItem->options[0]['options'][1]['enabled']);
        $this->assertTrue($menuItem->options[0]['options'][2]['enabled']);

        // Test that other groups are still enabled
        $this->assertTrue($menuItem->options[1]['enabled']);
        $this->assertTrue($menuItem->options[1]['options'][0]['enabled']);
        $this->assertTrue($menuItem->options[1]['options'][1]['enabled']);
        $this->assertTrue($menuItem->options[1]['options'][2]['enabled']);

        $this->assertTrue($menuItem->addons[0]['enabled']);
        $this->assertTrue($menuItem->addons[0]['options'][0]['enabled']);
        $this->assertTrue($menuItem->addons[0]['options'][1]['enabled']);
        $this->assertTrue($menuItem->addons[0]['options'][2]['enabled']);

        // Simulate disabling the addon group (only the group, not individual options)
        $menuItem->addons[0]['enabled'] = false;
        $menuItem->save();

        // Reload the menu item
        $menuItem->refresh();

        // Test that the addon group is disabled
        $this->assertFalse($menuItem->addons[0]['enabled']);

        // Test that individual addon options remain enabled (can still be edited)
        $this->assertTrue($menuItem->addons[0]['options'][0]['enabled']);
        $this->assertTrue($menuItem->addons[0]['options'][1]['enabled']);
        $this->assertTrue($menuItem->addons[0]['options'][2]['enabled']);

        // Test that option groups are still in their previous state
        $this->assertFalse($menuItem->options[0]['enabled']);
        $this->assertTrue($menuItem->options[1]['enabled']);

        // Clean up test data
        $menuItem->delete();
    }

    public function test_enabling_group_does_not_auto_enable_options(): void
    {
        $testUserId = 999979;
        $testMenuItemId = 999979;
        $testCategoryId = 999979;

        // Create test category
        $category = Category::firstOrCreate(
            ['id' => $testCategoryId],
            [
                'name' => 'Test Category Enable Test',
                'is_active' => true
            ]
        );

        // Create test user
        $user = User::firstOrCreate(
            ['id' => $testUserId],
            [
                'name' => 'Test User Enable Test',
                'email' => 'test-enabletest@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]
        );

        // Create test menu item with disabled group and disabled options
        $menuItem = MenuItem::firstOrCreate(
            ['id' => $testMenuItemId],
            [
                'category_id' => $category->id,
                'name' => 'Test Burger Enable Test',
                'description' => 'Test burger for enable testing',
                'price' => 15.00,
                'is_active' => true,
                'options' => [
                    [
                        'name' => 'Size',
                        'enabled' => false, // Group disabled
                        'rules' => ['required', 'one'],
                        'options' => [
                            ['name' => 'Small', 'enabled' => false], // Options also disabled
                            ['name' => 'Medium', 'enabled' => false],
                            ['name' => 'Large', 'enabled' => false]
                        ]
                    ]
                ]
            ]
        );

        // Test initial state - group and options are disabled
        $this->assertFalse($menuItem->options[0]['enabled']);
        $this->assertFalse($menuItem->options[0]['options'][0]['enabled']);
        $this->assertFalse($menuItem->options[0]['options'][1]['enabled']);
        $this->assertFalse($menuItem->options[0]['options'][2]['enabled']);

        // Enable the group (but don't auto-enable options)
        $menuItem->options[0]['enabled'] = true;
        $menuItem->save();

        // Reload the menu item
        $menuItem->refresh();

        // Test that the group is enabled but options remain disabled
        $this->assertTrue($menuItem->options[0]['enabled']);
        $this->assertFalse($menuItem->options[0]['options'][0]['enabled']);
        $this->assertFalse($menuItem->options[0]['options'][1]['enabled']);
        $this->assertFalse($menuItem->options[0]['options'][2]['enabled']);

        // Clean up test data
        $menuItem->delete();
    }
}
