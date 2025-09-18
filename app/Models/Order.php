<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'status',
        'subtotal',
        'tax',
        'total',
        'payment_status',
        'payment_provider',
        'payment_ref',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
