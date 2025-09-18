<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--force' => true]);
    }

    public function test_guest_cannot_access_admin_menu(): void
    {
        $this->get('/admin/menu')->assertRedirect('/login');
    }

    public function test_non_admin_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user)->get('/admin/menu')->assertStatus(403);
    }

    public function test_admin_can_see_menu_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cat = Category::create(['name' => 'Pizzas']);
        MenuItem::create(['name' => 'Margherita', 'category_id' => $cat->id, 'price' => 9.99, 'is_active' => true]);

        $this->actingAs($admin)->get('/admin/menu')
            ->assertOk()
            ->assertSee('Menu Items')
            ->assertSee('Margherita');
    }
}
