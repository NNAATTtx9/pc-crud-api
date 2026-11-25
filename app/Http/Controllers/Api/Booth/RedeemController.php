<?php

namespace App\Http\Controllers\Api\Booth;

use App\Http\Controllers\Controller;
use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedeemController extends Controller
{
    private const COST = 10;

    public function redeem(Request $request)
    {
        $code = strtoupper($request->validate(['qr_code' => 'required|string'])['qr_code']);
        $booth = $request->attributes->get('booth');

        try {
            return response()->json(DB::transaction(function () use ($code, $booth) {
                $registrant = Registrant::where('qr_code', $code)->lockForUpdate()->first();

                throw_if(! $registrant, RedeemFailure::class, 'Invalid QR code', 404);
                throw_if($registrant->redemptions()->where('booth_type', $booth->booth_type)->exists(),
                    RedeemFailure::class, 'Already redeemed at this booth', 409);
                throw_if($registrant->wallet_status !== 'active', RedeemFailure::class, 'Wallet is exhausted', 400);
                throw_if($registrant->credit_balance < self::COST, RedeemFailure::class, 'Insufficient balance', 400);

                $amounts = [
                    'amount'         => self::COST,
                    'balance_before' => $before = $registrant->credit_balance,
                    'balance_after'  => $after = $before - self::COST,
                ];

                $registrant->update([
                    'credit_balance' => $after,
                    'wallet_status'  => $after === 0 ? 'exhausted' : 'active',
                ]);

                $registrant->redemptions()->create($amounts + [
                    'booth_staff_id' => $booth->id,
                    'booth_type'     => $booth->booth_type,
                    'redeemed_at'    => now(),
                ]);

                return ['registrant' => $registrant->only('id', 'name')] + $amounts
                    + ['wallet_status' => $registrant->wallet_status];
            }));
        } catch (RedeemFailure $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    } 
}
