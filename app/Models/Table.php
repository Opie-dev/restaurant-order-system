<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'table_number',
        'capacity',
        'is_active',
        'location_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function qrCodes(): HasOne
    {
        return $this->hasOne(TableQrCode::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get display name for the table
     */
    public function getDisplayNameAttribute(): string
    {
        return "Table {$this->table_number}";
    }

    /**
     * Get current active order for this table
     */
    public function getCurrentOrderAttribute(): ?Order
    {
        return $this->orders()
            ->whereIn('status', ['pending', 'preparing', 'delivering'])
            ->latest()
            ->first();
    }

    /**
     * Check if table has an active order
     */
    public function hasActiveOrder(): bool
    {
        return $this->current_order !== null;
    }

    /**
     * Get active QR code for this table
     */
    public function getActiveQrCodeAttribute(): ?TableQrCode
    {
        return $this->qrCodes()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();
    }
}
