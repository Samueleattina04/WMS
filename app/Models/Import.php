<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Import extends Model
{
    use BelongsToCompany;

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'filename', 'type', 'status',
        'rows_total', 'rows_imported', 'rows_failed',
        'error_log', 'created_by_user_id',
    ];

    protected $casts = [
        'error_log' => 'array',
        'created_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'products' => 'Prodotti',
            'movements' => 'Movimenti',
            'stock' => 'Stock',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'processing' => 'In Elaborazione',
            'completed' => 'Completato',
            'failed' => 'Fallito',
            default => $this->status,
        };
    }
}
