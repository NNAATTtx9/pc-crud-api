<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $table = 'api_token';
    protected $primaryKey = 'token';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'token', 'owner_type', 'owner_id', 'expires_at',
    ];

    public static function issue(string $ownerType, int $ownerId): string
    {
        $token = Str::random(60);
        self::create([
            'token' => $token,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'expires_at' => now()->addHours($ownerType === 'admin' ? 24 : 8),
        ]);

        return $token;
    }
}
