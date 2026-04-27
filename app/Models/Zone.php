<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $fillable = ['company_id', 'warehouse_id', 'name', 'code', 'description', 'type', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shelves(): HasMany
    {
        return $this->hasMany(Shelf::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'receiving' => 'Ricezione',
            'storage' => 'Stoccaggio',
            'shipping' => 'Spedizione',
            'quality' => 'Controllo Qualità',
            default => $this->type,
        };
    }
}
