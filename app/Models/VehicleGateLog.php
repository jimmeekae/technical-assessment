<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleGateLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'gated_in_at' => 'datetime',
        'gated_out_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function gatedInUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gated_in_by');
    }

    public function gatedOutUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gated_out_by');
    }
}
