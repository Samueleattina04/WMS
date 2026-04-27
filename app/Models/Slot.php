<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slot extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id', 'shelf_id', 'code', 'level', 'column',
        'max_quantity', 'current_quantity', 'is_occupied', 'is_active',
    ];

    protected $casts = [
        'is_occupied' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class);
    }

    public function stockLocations(): HasMany
    {
        return $this->hasMany(StockLocation::class);
    }

    public function getOccupancyStatusAttribute(): string
    {
        if ($this->current_quantity === 0) {
            return 'empty';
        }
        if ($this->max_quantity && $this->current_quantity >= $this->max_quantity) {
            return 'full';
        }
        return 'partial';
    }

    public function getFullCodeAttribute(): string
    {
        $shelf = $this->shelf?->code ?? '';
        return $shelf ? "{$shelf}-{$this->code}" : $this->code;
    }
}
