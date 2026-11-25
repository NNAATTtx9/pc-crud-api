<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Redemption extends Model
{
    protected $table = 'redemption';
    public $timestamps = false;
    protected $fillable = [
        'registrant_id', 'booth_staff_id', 'booth_type', 'amount',
        'balance_before', 'balance_after', 'redeemed_at',
    ];
    public function registrant(): BelongsTo
    {
        return $this->belongsTo(Registrant::class);
    }
    public function boothStaff(): BelongsTo
    {
        return $this->belongsTo(BoothStaff::class);
    }
}
