<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'guest_token', 'store_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // Session-based cart methods
    public static function fromSession(array $cartData): self
    {
        $cart = new self();
        $cart->sessionData = $cartData;
        return $cart;
    }

    public function getSessionItems(): array
    {
        return $this->sessionData ?? [];
    }

    public function getSessionCount(): int
    {
        $count = 0;
        foreach ($this->sessionData ?? [] as $item) {
            $count += $item['qty'];
        }
        return $count;
    }
}
