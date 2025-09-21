<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'name_snapshot',
        'unit_price',
        'qty',
        'line_total',
        'selections',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'selections' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Get formatted selections for display
     */
    public function getFormattedSelections(): string
    {
        if (!$this->selections || empty($this->selections)) {
            return '';
        }

        $formatted = [];

        foreach ($this->selections as $type => $items) {
            if (is_array($items) && !empty($items)) {
                $formatted[] = ucfirst($type) . ': ' . implode(', ', $items);
            }
        }

        return implode(' | ', $formatted);
    }

    /**
     * Get selections as an array for easy access
     */
    public function getSelectionsArray(): array
    {
        return $this->selections ?? [];
    }

    /**
     * Check if item has any selections
     */
    public function hasSelections(): bool
    {
        return !empty($this->selections);
    }

    /**
     * Get selections by type (options, addons, etc.)
     */
    public function getSelectionsByType(string $type): array
    {
        return $this->selections[$type] ?? [];
    }
}
