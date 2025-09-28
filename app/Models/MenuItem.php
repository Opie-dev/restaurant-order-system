<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'base_price',
        'image_path',
        'is_active',
        'enabled',
        'position',
        'stock',
        'tag',
        'type',
        'options',
        'addons',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'enabled' => 'boolean',
        'options' => 'array',
        'addons' => 'array',
        'position' => 'integer',
        'stock' => 'integer',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('name');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
