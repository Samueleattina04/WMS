<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    use BelongsToCompany;

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'product_id', 'slot_from_id', 'slot_to_id',
        'type', 'quantity', 'lot_number', 'expiry_date',
        'document_number', 'document_type', 'notes', 'created_by_user_id',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function slotFrom(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'slot_from_id');
    }

    public function slotTo(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'slot_to_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'incoming' => 'Entrata',
            'outgoing' => 'Uscita',
            'transfer' => 'Trasferimento',
            'adjustment' => 'Rettifica',
            'return' => 'Reso',
            'damaged' => 'Danno/Scaduto',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'incoming' => 'green',
            'outgoing' => 'red',
            'transfer' => 'blue',
            'adjustment' => 'yellow',
            'return' => 'purple',
            'damaged' => 'gray',
            default => 'gray',
        };
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            'ddt' => 'DDT',
            'order' => 'Ordine',
            'transfer' => 'Trasferimento',
            'adjustment' => 'Rettifica',
            default => '',
        };
    }
}
