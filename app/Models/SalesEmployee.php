<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class SalesEmployee extends Model
{
    /** @use HasFactory<\Database\Factories\SalesEmployeeFactory> */
    use HasFactory;

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
