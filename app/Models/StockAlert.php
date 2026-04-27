<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'product_id', 'alert_type',
        'current_quantity', 'threshold_quantity', 'expiry_date',
        'is_resolved', 'notified_at', 'resolved_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_resolved' => 'boolean',
        'notified_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAlertTypeLabelAttribute(): string
    {
        return match ($this->alert_type) {
            'low_stock' => 'Scorta Bassa',
            'expiry' => 'Prossima Scadenza',
            'out_of_stock' => 'Esaurito',
            default => $this->alert_type,
        };
    }

    public function getAlertTypeColorAttribute(): string
    {
        return match ($this->alert_type) {
            'low_stock' => 'yellow',
            'expiry' => 'orange',
            'out_of_stock' => 'red',
            default => 'gray',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_resolved', false);
    }
}
