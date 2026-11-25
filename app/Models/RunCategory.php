<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class RunCategory extends Model
{
    protected $table = 'run_category';
    public $timestamps = false;
    protected $fillable = [
        'event_id', 'slug', 'name', 'description',
        'price', 'credits', 'is_active',
    ];
    protected $casts = [
        'price' => 'integer',
        'credits' => 'integer',
        'is_active' => 'boolean',
    ];
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
    public function toArrayPublic(): array
    {
        return $this->only(['id', 'slug', 'name', 'description', 'price',
            'credits']);
    }

    public function toArrayBrief(): array
    {
        return $this->only(['id', 'name']);
    }
}
