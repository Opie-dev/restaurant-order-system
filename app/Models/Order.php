<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    // Order status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PREPARING = 'preparing';
    const STATUS_DELIVERING = 'delivering';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment status constants
    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PROCESSING = 'processing';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'store_id',
        'address_id',
        'code',
        'status',
        'subtotal',
        'tax',
        'total',
        'payment_status',
        'payment_provider',
        'payment_ref',
        'tracking_url',
        'delivery_fee',
        'notes',
        'cancellation_remarks',
        'ship_recipient_name',
        'ship_phone',
        'ship_line1',
        'ship_line2',
        'ship_city',
        'ship_state',
        'ship_postal_code',
        'ship_country',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'order_items')
            ->withPivot(['name_snapshot', 'unit_price', 'qty', 'line_total'])
            ->withTimestamps();
    }

    /**
     * Get valid status transitions for the current order status
     */
    public function getValidTransitions(): array
    {
        $validTransitions = [
            self::STATUS_PENDING => [self::STATUS_PREPARING, self::STATUS_CANCELLED],
            self::STATUS_PREPARING => [self::STATUS_DELIVERING, self::STATUS_COMPLETED, self::STATUS_CANCELLED],
            self::STATUS_DELIVERING => [self::STATUS_COMPLETED, self::STATUS_CANCELLED],
            self::STATUS_COMPLETED => [],
            self::STATUS_CANCELLED => []
        ];

        return $validTransitions[$this->status] ?? [];
    }

    /**
     * Check if a status transition is valid
     */
    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, $this->getValidTransitions());
    }

    /**
     * Check if order is pending (needs attention)
     */
    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PREPARING]);
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Get status color class for UI
     */
    public function getStatusColorClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PREPARING => 'bg-orange-100 text-orange-800',
            self::STATUS_DELIVERING => 'bg-purple-100 text-purple-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get payment status color class for UI
     */
    public function getPaymentStatusColorClass(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_STATUS_PAID => 'bg-green-100 text-green-800',
            self::PAYMENT_STATUS_FAILED => 'bg-red-100 text-red-800',
            self::PAYMENT_STATUS_PROCESSING => 'bg-yellow-100 text-yellow-800',
            self::PAYMENT_STATUS_REFUNDED => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
