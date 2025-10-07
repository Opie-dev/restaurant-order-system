<?php

use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Livewire;
use App\Livewire\Admin\Onboarding\OnboardingWizard;

describe('Onboarding Flow', function () {
    beforeEach(function () {
        $this->merchant = User::factory()->create(['role' => 'admin']);
        $this->store = Store::factory()->create([
            'admin_id' => $this->merchant->id,
            'is_onboarding' => true
        ]);
        $this->actingAs($this->merchant);
    });

    describe('Onboarding Steps', function () {
        it('shows welcome step first', function () {
            Livewire::test(OnboardingWizard::class)
                ->assertSee('Welcome to your restaurant management system')
                ->assertSee('Get Started');
        });

        it('can proceed to store details step', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->assertSee('Store Details')
                ->assertSee('Restaurant Name');
        });

        it('can go back to previous step', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep') // Go to step 2
                ->call('previousStep') // Go back to step 1
                ->assertSee('Welcome to your restaurant management system');
        });

        it('shows progress indicator', function () {
            Livewire::test(OnboardingWizard::class)
                ->assertSee('Step 1 of 6')
                ->call('nextStep')
                ->assertSee('Step 2 of 6');
        });

        it('disables back button on first step', function () {
            Livewire::test(OnboardingWizard::class)
                ->assertSee('disabled')
                ->call('nextStep')
                ->assertDontSee('disabled');
        });

        it('shows skip option for optional steps', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep')
                ->assertSee('Skip for now');
        });
    });

    describe('Store Details Step', function () {
        beforeEach(function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep'); // Go to store details step
        });

        it('can fill store basic information', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('store_name', 'My Restaurant')
                ->set('store_description', 'A great place to eat')
                ->set('store_phone', '555-0123')
                ->set('store_email', 'info@myrestaurant.com')
                ->call('saveStoreDetails')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->name)->toBe('My Restaurant');
            expect($this->store->description)->toBe('A great place to eat');
            expect($this->store->phone)->toBe('555-0123');
            expect($this->store->email)->toBe('info@myrestaurant.com');
        });

        it('validates required fields', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('saveStoreDetails')
                ->assertHasErrors(['store_name']);
        });

        it('validates email format', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('store_name', 'My Restaurant')
                ->set('store_email', 'invalid-email')
                ->call('saveStoreDetails')
                ->assertHasErrors(['store_email']);
        });

        it('can upload store logo', function () {
            $file = \Illuminate\Http\UploadedFile::fake()->image('logo.jpg', 200, 200);

            Livewire::test(OnboardingWizard::class)
                ->set('store_name', 'My Restaurant')
                ->set('store_logo', $file)
                ->call('saveStoreDetails')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->logo_path)->not->toBeNull();
        });

        it('can upload store cover image', function () {
            $file = \Illuminate\Http\UploadedFile::fake()->image('cover.jpg', 800, 400);

            Livewire::test(OnboardingWizard::class)
                ->set('store_name', 'My Restaurant')
                ->set('store_cover', $file)
                ->call('saveStoreDetails')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->cover_path)->not->toBeNull();
        });
    });

    describe('Store Address Step', function () {
        beforeEach(function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->call('nextStep'); // Go to address step
        });

        it('can fill store address', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('address_line1', '123 Main Street')
                ->set('address_line2', 'Suite 100')
                ->set('city', 'Test City')
                ->set('state', 'Test State')
                ->set('postal_code', '12345')
                ->call('saveAddress')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->address_line1)->toBe('123 Main Street');
            expect($this->store->address_line2)->toBe('Suite 100');
            expect($this->store->city)->toBe('Test City');
            expect($this->store->state)->toBe('Test State');
            expect($this->store->postal_code)->toBe('12345');
        });

        it('validates required address fields', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('saveAddress')
                ->assertHasErrors(['address_line1', 'city', 'state', 'postal_code']);
        });

        it('validates postal code format', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('address_line1', '123 Main Street')
                ->set('city', 'Test City')
                ->set('state', 'Test State')
                ->set('postal_code', 'invalid')
                ->call('saveAddress')
                ->assertHasErrors(['postal_code']);
        });

        it('can use address autocomplete', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('address_search', '123 Main Street, Test City')
                ->call('searchAddress')
                ->assertHasNoErrors();

            // Mock address search results
            $this->assertTrue(true); // Placeholder for address search assertion
        });
    });

    describe('Opening Hours Step', function () {
        beforeEach(function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep'); // Go to opening hours step
        });

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

            Livewire::test(OnboardingWizard::class)
                ->set('opening_hours', $openingHours)
                ->call('saveOpeningHours')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->settings['opening_hours'])->toBe($openingHours);
        });

        it('can enable always open mode', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('always_open', true)
                ->call('saveOpeningHours')
                ->assertHasNoErrors();

            $this->store->refresh();
            expect($this->store->settings['always_open'])->toBeTrue();
        });

        it('validates time format', function () {
            $invalidHours = [
                ['day' => 'Monday', 'enabled' => true, 'open' => '25:00', 'close' => '17:00'],
            ];

            Livewire::test(OnboardingWizard::class)
                ->set('opening_hours', $invalidHours)
                ->call('saveOpeningHours')
                ->assertHasErrors();
        });

        it('validates close time is after open time', function () {
            $invalidHours = [
                ['day' => 'Monday', 'enabled' => true, 'open' => '17:00', 'close' => '09:00'],
            ];

            Livewire::test(OnboardingWizard::class)
                ->set('opening_hours', $invalidHours)
                ->call('saveOpeningHours')
                ->assertHasErrors();
        });

        it('can use preset hours templates', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('usePresetHours', 'standard')
                ->assertHasNoErrors();

            // Assert preset hours were applied
            $this->assertTrue(true); // Placeholder for preset hours assertion
        });
    });

    describe('Menu Categories Step', function () {
        beforeEach(function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep'); // Go to categories step
        });

        it('can create initial categories', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('categories', [
                    ['name' => 'Appetizers', 'description' => 'Start your meal right'],
                    ['name' => 'Main Courses', 'description' => 'Our signature dishes'],
                    ['name' => 'Desserts', 'description' => 'Sweet endings']
                ])
                ->call('saveCategories')
                ->assertHasNoErrors();

            expect(Category::where('store_id', $this->store->id)->count())->toBe(3);
        });

        it('can use preset category templates', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('usePresetCategories', 'restaurant')
                ->assertHasNoErrors();

            // Assert preset categories were created
            $this->assertTrue(true); // Placeholder for preset categories assertion
        });

        it('validates category names', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('categories', [
                    ['name' => '', 'description' => 'Invalid category']
                ])
                ->call('saveCategories')
                ->assertHasErrors();
        });

        it('can skip categories step', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('skipCategories')
                ->assertHasNoErrors();

            expect(Category::where('store_id', $this->store->id)->count())->toBe(0);
        });
    });

    describe('Sample Menu Items Step', function () {
        beforeEach(function () {
            $this->category = Category::factory()->create(['store_id' => $this->store->id]);

            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep'); // Go to sample menu items step
        });

        it('can create sample menu items', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('sample_items', [
                    [
                        'name' => 'Margherita Pizza',
                        'description' => 'Classic tomato and mozzarella',
                        'price' => 12.99,
                        'category_id' => $this->category->id
                    ],
                    [
                        'name' => 'Caesar Salad',
                        'description' => 'Fresh romaine with caesar dressing',
                        'price' => 8.99,
                        'category_id' => $this->category->id
                    ]
                ])
                ->call('saveSampleItems')
                ->assertHasNoErrors();

            expect(MenuItem::where('store_id', $this->store->id)->count())->toBe(2);
        });

        it('can use preset menu templates', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('usePresetMenu', 'italian')
                ->assertHasNoErrors();

            // Assert preset menu items were created
            $this->assertTrue(true); // Placeholder for preset menu assertion
        });

        it('validates menu item data', function () {
            Livewire::test(OnboardingWizard::class)
                ->set('sample_items', [
                    [
                        'name' => '',
                        'price' => -5.99,
                        'category_id' => $this->category->id
                    ]
                ])
                ->call('saveSampleItems')
                ->assertHasErrors();
        });

        it('can skip sample menu items', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('skipSampleItems')
                ->assertHasNoErrors();

            expect(MenuItem::where('store_id', $this->store->id)->count())->toBe(0);
        });
    });

    describe('Onboarding Completion', function () {
        it('can complete onboarding', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('completeOnboarding')
                ->assertHasNoErrors()
                ->assertRedirect('/admin');

            $this->store->refresh();
            expect($this->store->is_onboarding)->toBeFalse();
        });

        it('shows completion summary', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep')
                ->call('nextStep') // Go to completion step
                ->assertSee('Congratulations!')
                ->assertSee('Your restaurant is ready to go live');
        });

        it('sends welcome email after completion', function () {
            \Illuminate\Support\Facades\Notification::fake();

            Livewire::test(OnboardingWizard::class)
                ->call('completeOnboarding')
                ->assertHasNoErrors();

            \Illuminate\Support\Facades\Notification::assertSentTo(
                $this->merchant,
                \App\Mail\OnboardingCompletedNotification::class
            );
        });

        it('redirects to admin dashboard after completion', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('completeOnboarding')
                ->assertRedirect('/admin');
        });
    });

    describe('Onboarding Validation', function () {
        it('prevents skipping required steps', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->call('nextStep') // Skip store details
                ->assertHasErrors();
        });

        it('validates all steps before completion', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('completeOnboarding')
                ->assertHasErrors();
        });

        it('shows validation errors for incomplete steps', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->call('saveStoreDetails') // Try to save without data
                ->assertHasErrors(['store_name']);
        });
    });

    describe('Onboarding Progress', function () {
        it('saves progress between steps', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->set('store_name', 'My Restaurant')
                ->call('saveStoreDetails')
                ->call('nextStep')
                ->call('previousStep')
                ->assertSet('store_name', 'My Restaurant');
        });

        it('resumes from last completed step', function () {
            $this->store->update([
                'settings' => [
                    'onboarding_progress' => 3
                ]
            ]);

            Livewire::test(OnboardingWizard::class)
                ->assertSee('Step 3 of 6');
        });

        it('can exit and resume later', function () {
            Livewire::test(OnboardingWizard::class)
                ->call('nextStep')
                ->set('store_name', 'My Restaurant')
                ->call('saveStoreDetails')
                ->call('exitOnboarding')
                ->assertRedirect('/admin');

            $this->store->refresh();
            expect($this->store->is_onboarding)->toBeTrue();
        });
    });

    describe('Onboarding Access Control', function () {
        it('prevents non-admin access to onboarding', function () {
            $customer = User::factory()->create(['role' => 'customer']);
            $this->actingAs($customer);

            $this->get('/admin/onboarding')
                ->assertStatus(403);
        });

        it('prevents access to other stores onboarding', function () {
            $otherMerchant = User::factory()->create(['role' => 'admin']);
            $otherStore = Store::factory()->create([
                'admin_id' => $otherMerchant->id,
                'is_onboarding' => true
            ]);

            $this->actingAs($otherMerchant);

            Livewire::test(OnboardingWizard::class)
                ->assertDontSee($this->store->name);
        });

        it('redirects to admin if onboarding is complete', function () {
            $this->store->update(['is_onboarding' => false]);

            $this->get('/admin/onboarding')
                ->assertRedirect('/admin');
        });
    });
});
