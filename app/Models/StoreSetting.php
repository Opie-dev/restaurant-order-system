<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'description',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'phone',
        'email',
        'logo_path',
    ];

    /**
     * Get the first (and only) store setting record
     */
    public static function getSettings(): ?self
    {
        return self::first();
    }

    /**
     * Create or update store settings
     */
    public static function updateSettings(array $data): self
    {
        $settings = self::first();

        if ($settings) {
            $settings->update($data);
            return $settings;
        }

        return self::create($data);
    }
}
