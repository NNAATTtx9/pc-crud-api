<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'event';
    public $timestamps = false;
    protected $fillable = [
        'description', 'banner_image', 'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    protected function categories(): HasMany
    {
        return $this->hasMany(RunCategory::class);
    }

    public function toArrayPublic(): array
    {
        return $this ->only(['id', 'name', 'tagline', 'event_date', 'location',
            'description', 'banner_image']);
    }
}
