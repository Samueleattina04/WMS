<?php

namespace App\Models;

use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'type', 'name', 'content', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];
}
