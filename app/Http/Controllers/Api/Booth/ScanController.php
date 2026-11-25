<?php

namespace App\Http\Controllers\Api\Booth;

use App\Http\Controllers\Controller;
use App\Models\Registrant;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function scan(Request $request)
    {
        $code = strtoupper($request->validate(['qr_code' => 'required|string'])['qr_code']);
        $registrant = Registrant::firstWhere('qr_code', $code);

        if (! $registrant) {
            return response()->json(['error' => 'Invalid QR code'], 404);
        }

        return response()->json([
            'registrant'        => $registrant->only('id', 'name'),
            'credit_balance'    => $registrant->credit_balance,
            'wallet_status'     => $registrant->wallet_status,
            'has_redeemed_here' => $registrant->redemptions()
                ->where('booth_type', $request->attributes->get('booth')->booth_type)
                ->exists(),
        ]);
    }
}