<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id', 'category_id', 'supplier_id', 'unit_id',
        'sku', 'barcode', 'name', 'description',
        'min_stock_alert', 'reorder_quantity',
        'cost_price', 'selling_price',
        'has_expiry', 'has_lot_tracking',
        'weight', 'dimensions',
        'is_active', 'notes', 'image',
    ];

    protected $casts = [
        'has_expiry' => 'boolean',
        'has_lot_tracking' => 'boolean',
        'is_active' => 'boolean',
        'dimensions' => 'array',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_id');
    }

    public function stockLocations(): HasMany
    {
        return $this->hasMany(StockLocation::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->stockLocations()->sum('quantity');
    }

    public function isLowStock(): bool
    {
        return $this->min_stock_alert > 0 && $this->total_stock <= $this->min_stock_alert;
    }

    public function isOutOfStock(): bool
    {
        return $this->total_stock === 0;
    }
}
