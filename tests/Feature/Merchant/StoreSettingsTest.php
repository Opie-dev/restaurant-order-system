<?php

use App\Models\User;
use App\Models\Store;
use Livewire\Livewire;
use App\Livewire\Admin\Settings\StoreDetails;
use App\Livewire\Admin\Settings\StoreAddress;
use App\Livewire\Admin\Settings\StoreHours;
use App\Livewire\Admin\Settings\Security;

describe('Store Settings', function () {
    beforeEach(function () {
        $this->merchant = User::factory()->create(['role' => 'admin']);
        $this->store = Store::factory()->create(['admin_id' => $this->merchant->id]);
        $this->actingAs($this->merchant);
    });

    describe('Store Details Settings', function () {
        it('can update store basic information', function () {
            Livewire::test(StoreDetails::class)
                ->set('name', 'Updated Restaurant')
                ->set('description', 'Updated restaurant description')
                ->set('phone', '555-1234')
                ->set('email', 'updated@restaurant.com')
                ->call('save')
                ->assertHasNoErrors()
                ->assertDispatched('store-updated');

            $this->store->refresh();
            expect($this->store->name)->toBe('Updated Restaurant');
            expect($this->store->description)->toBe('Updated restaurant description');
            expect($this->store->phone)->toBe('555-1234');
            expect($this->store->email)->toBe('updated@restaurant.com');
        });

        it('validates required fields', function () {
            Livewire::test(StoreDetails::class)
                ->set('name', '')
                ->call('save')
                ->assertHasErrors(['name']);
        });

        it('validates email format', function () {
            Livewire::test(StoreDetails::class)
                ->set('email', 'invalid-email')
                ->call('save')
                ->assertHasErrors(['email']);
        });

        it('can upload store logo', function () {
            $file = \Illuminate\Http\UploadedFile::fake()->image('logo.jpg', 200, 200);

            Livewire::test(StoreDetails::class)
                ->set('logo', $file)
                ->call('save')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->logo_path)->not->toBeNull();
        });

        it('can upload store cover image', function () {
            $file = \Illuminate\Http\UploadedFile::fake()->image('cover.jpg', 800, 400);

            Livewire::test(StoreDetails::class)
                ->set('cover', $file)
                ->call('save')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->cover_path)->not->toBeNull();
        });
    });

    describe('Store Address Settings', function () {
        it('can update store address', function () {
            Livewire::test(StoreAddress::class)
                ->set('address_line1', '123 New Street')
                ->set('address_line2', 'Suite 100')
                ->set('city', 'New City')
                ->set('state', 'New State')
                ->set('postal_code', '12345')
                ->call('save')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->address_line1)->toBe('123 New Street');
            expect($this->store->address_line2)->toBe('Suite 100');
            expect($this->store->city)->toBe('New City');
            expect($this->store->state)->toBe('New State');
            expect($this->store->postal_code)->toBe('12345');
        });

        it('validates required address fields', function () {
            Livewire::test(StoreAddress::class)
                ->set('address_line1', '')
                ->set('city', '')
                ->set('state', '')
                ->set('postal_code', '')
                ->call('save')
                ->assertHasErrors(['address_line1', 'city', 'state', 'postal_code']);
        });

        it('validates postal code format', function () {
            Livewire::test(StoreAddress::class)
                ->set('postal_code', 'invalid')
                ->call('save')
                ->assertHasErrors(['postal_code']);
        });

        it('can get formatted address', function () {
            $this->store->update([
                'address_line1' => '123 Main St',
                'address_line2' => 'Suite 100',
                'city' => 'Test City',
                'state' => 'Test State',
                'postal_code' => '12345'
            ]);

            expect($this->store->address)->toBe('123 Main St, Suite 100, Test City, Test State, 12345');
        });
    });

    describe('Store Hours Settings', function () {
        it('can set opening hours for each day', function () {
            $openingHours = [
                ['day' => 'Monday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Tuesday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Wednesday', 'enabled' => false, 'open' => '', 'close' => ''],
                ['day' => 'Thursday', 'enabled' => true, 'open' => '10:00', 'close' => '18:00'],
                ['day' => 'Friday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                ['day' => 'Saturday', 'enabled' => true, 'open' => '10:00', 'close' => '16:00'],
                ['day' => 'Sunday', 'enabled' => false, 'open' => '', 'close' => ''],
            ];

            Livewire::test(StoreHours::class)
                ->set('opening_hours', $openingHours)
                ->call('save')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->settings['opening_hours'])->toBe($openingHours);
        });

        it('can enable always open mode', function () {
            Livewire::test(StoreHours::class)
                ->set('always_open', true)
                ->call('save')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->settings['always_open'])->toBeTrue();
        });

        it('validates time format', function () {
            $invalidHours = [
                ['day' => 'Monday', 'enabled' => true, 'open' => '25:00', 'close' => '17:00'],
            ];

            Livewire::test(StoreHours::class)
                ->set('opening_hours', $invalidHours)
                ->call('save')
                ->assertHasErrors();
        });

        it('validates close time is after open time', function () {
            $invalidHours = [
                ['day' => 'Monday', 'enabled' => true, 'open' => '17:00', 'close' => '09:00'],
            ];

            Livewire::test(StoreHours::class)
                ->set('opening_hours', $invalidHours)
                ->call('save')
                ->assertHasErrors();
        });

        it('can check if store is open based on hours', function () {
            $this->store->update([
                'settings' => [
                    'opening_hours' => [
                        ['day' => 'Monday', 'enabled' => true, 'open' => '09:00', 'close' => '17:00'],
                    ],
                    'always_open' => false
                ]
            ]);

            // Mock current time to Monday 10:00 AM
            $this->travelTo(now()->startOfWeek()->setTime(10, 0));

            expect($this->store->isCurrentlyOpen())->toBeTrue();
        });
    });

    describe('Security Settings', function () {
        it('can update password', function () {
            Livewire::test(Security::class)
                ->set('current_password', 'password')
                ->set('new_password', 'newpassword123')
                ->set('new_password_confirmation', 'newpassword123')
                ->call('updatePassword')
                ->assertHasNoErrors()
                ->assertRedirect('/login');

            $this->merchant->refresh();
            expect(\Illuminate\Support\Facades\Hash::check('newpassword123', $this->merchant->password))->toBeTrue();
        });

        it('validates current password', function () {
            Livewire::test(Security::class)
                ->set('current_password', 'wrongpassword')
                ->set('new_password', 'newpassword123')
                ->set('new_password_confirmation', 'newpassword123')
                ->call('updatePassword')
                ->assertHasErrors(['current_password']);
        });

        it('validates password confirmation', function () {
            Livewire::test(Security::class)
                ->set('current_password', 'password')
                ->set('new_password', 'newpassword123')
                ->set('new_password_confirmation', 'differentpassword')
                ->call('updatePassword')
                ->assertHasErrors(['new_password']);
        });

        it('can deactivate store', function () {
            Livewire::test(Security::class)
                ->set('store_deactivation_password', 'password')
                ->call('deactivateStore')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->is_active)->toBeFalse();
        });

        it('validates password for store deactivation', function () {
            Livewire::test(Security::class)
                ->set('store_deactivation_password', 'wrongpassword')
                ->call('deactivateStore')
                ->assertHasErrors(['store_deactivation_password']);
        });

        it('can delete account', function () {
            Livewire::test(Security::class)
                ->set('account_deletion_password', 'password')
                ->set('account_deletion_confirmation', 'DELETE')
                ->call('deleteAccount')
                ->assertHasNoErrors()
                ->assertRedirect('/');

            expect(\App\Models\User::withTrashed()->find($this->merchant->id))->not->toBeNull();
            expect(\App\Models\User::find($this->merchant->id))->toBeNull(); // Soft deleted
        });

        it('validates password and confirmation for account deletion', function () {
            Livewire::test(Security::class)
                ->set('account_deletion_password', 'wrongpassword')
                ->set('account_deletion_confirmation', 'DELETE')
                ->call('deleteAccount')
                ->assertHasErrors(['account_deletion_password']);

            Livewire::test(Security::class)
                ->set('account_deletion_password', 'password')
                ->set('account_deletion_confirmation', 'WRONG')
                ->call('deleteAccount')
                ->assertHasErrors(['account_deletion_confirmation']);
        });
    });

    describe('Settings Access Control', function () {
        it('prevents access to other merchants store settings', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create(['admin_id' => $otherMerchant->id]);

            $this->actingAs($otherMerchant);

            Livewire::test(StoreDetails::class)
                ->assertDontSee($this->store->name);
        });

        it('requires admin role to access settings', function () {
            $customer = User::factory()->create(['role' => 'customer']);

            $this->actingAs($customer);

            $this->get('/admin/settings/store-details')
                ->assertStatus(403);
        });
    });
});
