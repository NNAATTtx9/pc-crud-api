<?php

namespace App\Services;

use App\Models\Registrant;
use RuntimeException;

class QrCodeGenerator
{
    private const ALPHABET = '23456789ABCDEFGHJKMNPQSTUVWXYZ';
    private const LENGTH = 6;
    private const MAX_ATTEMPTS = 20;

    public function unique(): string
    {
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = $this -> random();
            if (! Registrant::where('qr_code', $code)->exists()) {
                return $code;
            }
        }
        throw new RuntimeException('Could not generate a unique OR code');
    }
    private function random(): string
    {
        $code = '';
        $max = strlen(self::ALPHABET) - 1;
        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }
        return $code;
    }
}
