<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoothStaff extends Model
{
    protected $table = 'booth_staff';
    public $timestamps = false;
    protected $fillable = [
        'username', 'password', 'booth_type', 'is_active',
    ];
    protected $hidden = ['password'];
    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }
}
