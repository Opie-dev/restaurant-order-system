<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Store;
use App\Services\Admin\StoreService;
use App\Constants\CountryCodes;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\Admin\OnboardingService;

#[Layout('layouts.admin')]
class StoreDetails extends Component
{
    use WithFileUploads;
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255|unique:stores,slug')]
    public string $slug = '';

    #[Validate('nullable|string|max:500')]
    public ?string $description = null;

    #[Validate('required|string|max:255')]
    public ?string $address_line1 = '';

    #[Validate('nullable|string|max:255')]
    public ?string $address_line2 = null;

    #[Validate('required|string|max:100')]
    public ?string $city = '';

    #[Validate('required|string|max:100')]
    public ?string $state = '';

    #[Validate('required|string|max:20')]
    public ?string $postal_code = '';

    #[Validate('required|string|max:20|regex:/^[0-9+\-\s()]+$/')]
    public ?string $phone = '';

    #[Validate('required|email')]
    public ?string $email = '';

    #[Validate('nullable|string|max:10')]
    public ?string $country_code = null;

    #[Validate('nullable|mimes:jpg,jpeg,png,gif,bmp,svg,webp,avif|max:2048')]
    public $logo;

    #[Validate('nullable|mimes:jpg,jpeg,png,gif,bmp,svg,webp,avif|max:4096')]
    public $cover;

    #[Validate('nullable|mimes:jpg,jpeg,png,gif,bmp,svg,webp,avif|max:8192')]
    public $cover_desktop;

    public ?string $logo_path = null;
    public ?string $cover_path = null;
    public ?string $cover_desktop_path = null;
    public ?Store $currentStore = null;

    private $storeService;
    private $onboardingService;

    public bool $always_open = false;
    /**
     * @var array<int, array{day:string,enabled:bool,open:string|null,close:string|null}>
     */
    public array $hours = [];

    // Social media and links
    #[Validate('nullable|string|max:500')]
    public ?string $social_google_map = null;
    #[Validate('nullable|string|max:255')]
    public ?string $social_facebook = null;
    #[Validate('nullable|string|max:255')]
    public ?string $social_tiktok = null;
    #[Validate('nullable|string|max:255')]
    public ?string $social_other = null;
    #[Validate('nullable|string|max:255')]
    public ?string $social_instagram = null;
    #[Validate('nullable|string|max:255')]
    public ?string $social_youtube = null;

    #[Validate('nullable|numeric|min:0|max:100')]
    public ?float $tax_rate = null;

    protected function taxRules()
    {
        return [
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }

    protected function socialMediaRules()
    {
        return [
            'social_google_map' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|string|max:255',
            'social_tiktok' => 'nullable|string|max:255',
            'social_other' => 'nullable|string|max:255',
            'social_instagram' => 'nullable|string|max:255',
            'social_youtube' => 'nullable|string|max:255',
        ];
    }

    public function boot()
    {
        $this->storeService = new StoreService();
        // Load current store from StoreService
        $this->currentStore = $this->storeService->getCurrentStore();
        $this->onboardingService = new OnboardingService($this->currentStore);
    }

    public function mount()
    {
        if (!$this->currentStore) {
            $this->redirectRoute('admin.stores.select');
            return;
        }

        if ($this->currentStore) {
            $this->name = $this->currentStore->name ?? '';
            $this->slug = $this->currentStore->slug ?? '';
            $this->description = $this->currentStore->description;
            $this->address_line1 = $this->currentStore->address_line1 ?? '';
            $this->address_line2 = $this->currentStore->address_line2;
            $this->city = $this->currentStore->city ?? '';
            $this->state = $this->currentStore->state ?? '';
            $this->postal_code = $this->currentStore->postal_code ?? '';
            $this->phone = $this->currentStore->phone ?? '';
            $this->email = $this->currentStore->email ?? '';
            // Add + prefix to country_code for display (stored without +)
            $storedCountryCode = $this->currentStore->country_code ?? '60';
            $this->country_code = '+' . ltrim($storedCountryCode, '+');
            $this->logo_path = $this->currentStore->logo_path;
            $this->cover_path = $this->currentStore->cover_path;

            $settings = $this->currentStore->settings ?? [];
            $this->always_open = (bool)($settings['always_open'] ?? false);
            $this->hours = $this->loadOpeningHoursFromSettings($settings['opening_hours'] ?? null);

            // Load socials
            $social = $settings['social'] ?? [];
            $this->social_google_map = $social['google_map'] ?? null;
            $this->social_facebook = $social['facebook'] ?? null;
            $this->social_tiktok = $social['tiktok'] ?? null;
            $this->social_other = $social['other'] ?? null;
            $this->social_instagram = $social['instagram'] ?? null;
            $this->social_youtube = $social['youtube'] ?? null;

            // Load tax rate
            $this->tax_rate = $this->currentStore->tax_rate;
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stores,slug,' . ($this->currentStore ? $this->currentStore->id : 'NULL'),
            'description' => 'nullable|string|max:500',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country_code' => 'required|string',
            'phone' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $error = CountryCodes::getPhoneNumberValidationError($this->country_code, (string)$value);
                    if ($error) {
                        $fail($error);
                    }
                }
            ],
            'email' => 'required|email',
        ];
    }

    /**
     * @param mixed $saved
     * @return array<int, array{day:string,enabled:bool,open:string|null,close:string|null}>
     */
    private function loadOpeningHoursFromSettings($saved): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $defaults = [];
        foreach ($days as $d) {
            $defaults[] = [
                'day' => ucfirst($d),
                'enabled' => false,
                'open' => '08:00',
                'close' => '23:00',
            ];
        }

        if (!is_array($saved)) {
            return $defaults;
        }

        $out = [];
        foreach ($defaults as $index => $row) {
            $savedRow = $saved[$index] ?? null;
            if (is_array($savedRow)) {
                $out[] = [
                    'day' => $row['day'],
                    'enabled' => (bool)($savedRow['enabled'] ?? false),
                    'open' => $savedRow['open'] ?? $row['open'],
                    'close' => $savedRow['close'] ?? $row['close'],
                ];
            } else {
                $out[] = $row;
            }
        }
        return $out;
    }

    public function saveDetails(): void
    {
        $this->validate($this->rules());

        if (!$this->currentStore) {
            $this->dispatch('flash', type: 'error', message: 'No store selected.');
            return;
        }

        $this->currentStore->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'country_code' => ltrim($this->country_code, '+'),
        ]);

        $this->dispatch('flash', type: 'success', message: 'Store details updated successfully.');

        if (!$this->onboardingService->isOnboardingComplete()) {
            $this->redirectRoute('admin.dashboard');
        }
    }

    public function saveMedia(): void
    {
        $this->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:2048',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:4096',
            'cover_desktop' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:8192',
        ]);

        if (!$this->currentStore) {
            $this->dispatch('flash', type: 'error', message: 'No store selected.');
            return;
        }

        $data = [];

        // Handle logo upload
        if ($this->logo) {
            if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
                Storage::disk('public')->delete($this->logo_path);
            }
            $logoPath = $this->logo->store('logos', 'public');
            $data['logo_path'] = $logoPath;
            $this->logo_path = $logoPath;
        }

        // Handle cover upload
        if ($this->cover) {
            if ($this->cover_path && Storage::disk('public')->exists($this->cover_path)) {
                Storage::disk('public')->delete($this->cover_path);
            }
            $coverPath = $this->cover->store('covers', 'public');
            $data['cover_path'] = $coverPath;
            $this->cover_path = $coverPath;
        }

        if (!empty($data)) {
            $this->currentStore->update($data);
        }

        // Clear uploads after successful save
        $this->logo = null;
        $this->cover = null;

        $this->dispatch('flash', type: 'success', message: 'Store media updated successfully.');

        // Check if store is in onboarding mode
        if (!$this->onboardingService->isOnboardingComplete()) {
            $this->redirectRoute('admin.dashboard');
        }

        $this->dispatch('media-saved');
    }

    public function saveHours(): void
    {
        if (!$this->currentStore) {
            $this->dispatch('flash', type: 'error', message: 'No store selected.');
            return;
        }

        // Validate structure
        foreach ($this->hours as $i => $row) {
            $enabled = (bool)($row['enabled'] ?? false);
            $open = $row['open'] ?? null;
            $close = $row['close'] ?? null;
            if ($enabled) {
                if (!$open || !$close) {
                    $this->addError("hours.$i", 'Opening and closing time are required for enabled days.');
                    return;
                }
                // Simple HH:MM pattern check
                if (!preg_match('/^\d{2}:\d{2}$/', $open) || !preg_match('/^\d{2}:\d{2}$/', $close)) {
                    $this->addError("hours.$i", 'Time must be in HH:MM format.');
                    return;
                }
                if (strtotime($open) >= strtotime($close)) {
                    $this->addError("hours.$i", 'Closing time must be after opening time.');
                    return;
                }
            }
        }

        $settings = $this->currentStore->settings ?? [];
        $settings['always_open'] = $this->always_open;
        $settings['opening_hours'] = $this->hours;
        $this->currentStore->update(['settings' => $settings]);

        $this->dispatch('flash', type: 'success', message: 'Opening hours updated successfully.');

        // Check if store is in onboarding mode
        if ($this->currentStore->is_onboarding) {
            $this->redirectRoute('admin.dashboard');
        }

        $this->dispatch('hours-saved');
    }

    public function updatedAlwaysOpen($value): void
    {
        // Don't modify the hours data when toggling always_open
        // Just keep the current day data intact
    }

    public function saveSocialMedia(): void
    {
        if (!$this->currentStore) {
            $this->dispatch('flash', type: 'error', message: 'No store selected.');
            return;
        }

        $this->validate($this->socialMediaRules());

        $settings = $this->currentStore->settings ?? [];
        $settings['social'] = [
            'google_map' => $this->social_google_map,
            'facebook' => $this->social_facebook,
            'tiktok' => $this->social_tiktok,
            'other' => $this->social_other,
            'instagram' => $this->social_instagram,
            'youtube' => $this->social_youtube,
        ];
        $this->currentStore->update(['settings' => $settings]);

        $this->dispatch('flash', type: 'success', message: 'Social media links updated successfully.');
        $this->dispatch('social-saved');
    }

    public function saveTax(): void
    {
        $this->validate($this->taxRules());

        if (!$this->currentStore) {
            $this->dispatch('flash', type: 'error', message: 'No store selected.');
            return;
        }

        $this->currentStore->update([
            'tax_rate' => $this->tax_rate,
        ]);

        $this->dispatch('flash', type: 'success', message: 'Tax settings updated successfully.');
    }

    public function getCountryCodesProperty(): array
    {
        return CountryCodes::getAll();
    }

    public function phone_validation($attribute, $value, $fail)
    {
        $countryCode = $this->country_code;
        if (!$countryCode) {
            $fail('Country code is required for phone validation.');
            return;
        }

        $error = CountryCodes::getPhoneNumberValidationError($countryCode, (string)$value);
        if ($error) {
            $fail($error);
        }
    }

    public function parsePhoneNumber(string $phone): array
    {
        // Remove any non-digit characters except +
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);

        // Find matching country code
        $countries = CountryCodes::getAll();
        $matchedCountry = null;
        $phoneNumber = $phone;

        foreach ($countries as $country) {
            $code = ltrim($country['code'], '+');
            if (str_starts_with($cleanPhone, $code)) {
                $matchedCountry = $country;
                $phoneNumber = substr($cleanPhone, strlen($code));
                break;
            }
        }

        return [
            'country' => $matchedCountry,
            'phone_number' => $phoneNumber,
            'full_phone' => $phone
        ];
    }

    public function render()
    {
        return view('livewire.admin.settings.store-details');
    }
}
