<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registrant extends Model
{
    protected $table = 'registrant';
    public $timestamps = false;
    protected $fillable = [
        'event_id', 'category_id', 'name', 'email', 'mobile', 'tshirt_size', 'organisation',
        'donation_amount', 'qr_code', 'credit_balance', 'wallet_status', 'goodie_status',
        'pdpa_consent', 'registered_at',
    ];
    protected $casts = [
        'donation_amount' => 'integer',
        'credit_balance' => 'integer',
        'pdpa_consent' =>  'boolean',
    ];
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(RunCategory::class);
    }
    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }
    public function toArrayCreated(): array
    {
        return $this->only(['id', 'name', 'qr_code', 'category', 'donation_amount',
            'credit_balance']);
    }

    public function toArrayPublic(): array
    {
        return[
            ...$this->only(['id', 'name', 'qr_code', 'donation_amount', 'credit_balance',
            'tshirt_size', 'registered_at']),
            'category' => $this->category?->toArrayBrief(),
            ];
    }
    public function toArrayAdmin(): array
    {
        return [
            ...$this->only(['id', 'name', 'email', 'mobile', 'tshirt_size', 'organisation',
            'donation_amount', 'qr_code', 'credit_balance', 'wallet_status', 'goodie_status',
            'registered_at',]),
            'category' => $this->category?->toArrayBrief(),
            ];
    }
}
