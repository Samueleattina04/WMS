<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'address', 'logo',
        'vat_number', 'fiscal_code', 'subscription_plan', 'is_active', 'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function alertDaysBeforeExpiry(): int
    {
        return (int) $this->getSetting('alert_days_before_expiry', config('wms.alert_days_before_expiry', 30));
    }

    public function alertEmails(): array
    {
        return $this->getSetting('alert_emails', []) ?: [];
    }
}
