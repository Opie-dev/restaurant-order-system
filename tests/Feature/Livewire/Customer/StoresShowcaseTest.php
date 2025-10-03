<?php

namespace Tests\Feature\Livewire\Customer;

use App\Livewire\Customer\StoresShowcase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class StoresShowcaseTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(StoresShowcase::class)
            ->assertStatus(200);
    }
}
