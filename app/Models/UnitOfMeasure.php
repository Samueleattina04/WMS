<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitOfMeasure extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $table = 'units_of_measure';

    protected $fillable = ['company_id', 'name', 'abbreviation', 'type'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'unit_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'quantity' => 'Quantità',
            'weight' => 'Peso',
            'volume' => 'Volume',
            'length' => 'Lunghezza',
            default => $this->type,
        };
    }
}
