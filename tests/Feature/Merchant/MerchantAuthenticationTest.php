<?php

use App\Models\User;
use App\Models\Store;
use Livewire\Livewire;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;

describe('Merchant Authentication', function () {
    beforeEach(function () {
        $this->merchant = User::factory()->create([
            'role' => 'admin',
            'email' => 'merchant@example.com',
            'password' => bcrypt('password123')
        ]);
    });

    it('can login with valid credentials', function () {
        Livewire::test(Login::class)
            ->set('email', 'merchant@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect('/admin');

        expect(auth()->check())->toBeTrue();
        expect(auth()->user()->email)->toBe('merchant@example.com');
        expect(auth()->user()->role)->toBe('admin');
    });

    it('cannot login with invalid credentials', function () {
        Livewire::test(Login::class)
            ->set('email', 'merchant@example.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email']);

        expect(auth()->check())->toBeFalse();
    });

    it('cannot login with non-existent email', function () {
        Livewire::test(Login::class)
            ->set('email', 'nonexistent@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);

        expect(auth()->check())->toBeFalse();
    });

    it('validates required fields', function () {
        Livewire::test(Login::class)
            ->call('login')
            ->assertHasErrors(['email', 'password']);
    });

    it('validates email format', function () {
        Livewire::test(Login::class)
            ->set('email', 'invalid-email')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);
    });

    it('can register new merchant account', function () {
        Livewire::test(Register::class)
            ->set('name', 'New Merchant')
            ->set('email', 'newmerchant@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect('/admin');

        expect(User::where('email', 'newmerchant@example.com')->exists())->toBeTrue();

        $user = User::where('email', 'newmerchant@example.com')->first();
        expect($user->role)->toBe('admin');
        expect(auth()->check())->toBeTrue();
    });

    it('validates password confirmation during registration', function () {
        Livewire::test(Register::class)
            ->set('name', 'New Merchant')
            ->set('email', 'newmerchant@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'differentpassword')
            ->call('register')
            ->assertHasErrors(['password']);
    });

    it('prevents duplicate email registration', function () {
        Livewire::test(Register::class)
            ->set('name', 'Duplicate Merchant')
            ->set('email', 'merchant@example.com') // Same as existing merchant
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    });

    it('validates minimum password length', function () {
        Livewire::test(Register::class)
            ->set('name', 'New Merchant')
            ->set('email', 'newmerchant@example.com')
            ->set('password', '123')
            ->set('password_confirmation', '123')
            ->call('register')
            ->assertHasErrors(['password']);
    });

    it('can logout successfully', function () {
        auth()->login($this->merchant);
        expect(auth()->check())->toBeTrue();

        $this->post('/logout');

        expect(auth()->check())->toBeFalse();
    });

    it('redirects authenticated merchant to admin dashboard', function () {
        auth()->login($this->merchant);

        $this->get('/login')
            ->assertRedirect('/admin');
    });

    it('redirects unauthenticated user to login', function () {
        $this->get('/admin')
            ->assertRedirect('/login');
    });
});
