<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image_path',
        'is_active',
        'position',
        'stock',
        'tag',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
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
}
