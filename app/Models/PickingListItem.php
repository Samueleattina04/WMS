<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickingListItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'picking_list_id', 'product_id', 'slot_id',
        'quantity_required', 'quantity_picked', 'lot_number', 'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function pickingList(): BelongsTo
    {
        return $this->belongsTo(PickingList::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }
}
