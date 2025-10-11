<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'phone',
        'email',
        'country_code',
        'logo_path',
        'cover_path',
        'settings',
        'tax_rate',
        'is_active',
        'is_onboarding',
        'admin_id',
    ];

    protected $casts = [
        'settings' => 'array',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'is_onboarding' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getAddressAttribute(): string
    {
        return "{$this->address_line1}, {$this->address_line2}, {$this->city}, {$this->state}, {$this->postal_code}";
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if the store is currently open based on opening hours
     */
    public function isCurrentlyOpen(): bool
    {
        $settings = $this->settings ?? [];

        // If always open is enabled, store is always available
        if ($settings['always_open'] ?? false) {
            return true;
        }

        $openingHours = $settings['opening_hours'] ?? [];
        if (empty($openingHours)) {
            return false; // No hours set means closed
        }

        $currentDay = strtolower(now()->format('l')); // monday, tuesday, etc.
        $currentTime = now()->format('H:i');

        // Find today's hours
        $todayHours = null;
        foreach ($openingHours as $dayHours) {
            if (strtolower($dayHours['day']) === $currentDay) {
                $todayHours = $dayHours;
                break;
            }
        }

        // If no hours for today or day is disabled, store is closed
        if (!$todayHours || !($todayHours['enabled'] ?? false)) {
            return false;
        }

        $openTime = $todayHours['open'] ?? null;
        $closeTime = $todayHours['close'] ?? null;

        if (!$openTime || !$closeTime) {
            return false;
        }

        // Check if current time is within opening hours
        return $currentTime >= $openTime && $currentTime <= $closeTime;
    }

    /**
     * Get the next opening time for display
     */
    public function getNextOpeningTime(): ?string
    {
        $settings = $this->settings ?? [];

        if ($settings['always_open'] ?? false) {
            return null; // Always open
        }

        $openingHours = $settings['opening_hours'] ?? [];
        if (empty($openingHours)) {
            return null;
        }

        $currentDay = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        // Check if store is open today
        foreach ($openingHours as $dayHours) {
            if (strtolower($dayHours['day']) === $currentDay && ($dayHours['enabled'] ?? false)) {
                $openTime = $dayHours['open'] ?? null;
                if ($openTime && $currentTime < $openTime) {
                    return "Opens today at {$openTime}";
                }
            }
        }

        // Find next available day
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $currentDayIndex = array_search($currentDay, $days);

        for ($i = 1; $i <= 7; $i++) {
            $nextDayIndex = ($currentDayIndex + $i) % 7;
            $nextDay = $days[$nextDayIndex];

            foreach ($openingHours as $dayHours) {
                if (strtolower($dayHours['day']) === $nextDay && ($dayHours['enabled'] ?? false)) {
                    $openTime = $dayHours['open'] ?? null;
                    if ($openTime) {
                        $dayName = ucfirst($nextDay);
                        return "Opens {$dayName} at {$openTime}";
                    }
                }
            }
        }

        return null;
    }
}
