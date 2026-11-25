<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Event;
use App\Models\Registrant;
use App\Models\RunCategory;
use App\Services\QrCodeGenerator;

class RegistrationController extends Controller
{
    public function __construct(private QrCodeGenerator $qrCodes) {}

    public function store(RegisterRequest $request)
    {
        $category = RunCategory::findOrFail($request->category_id);
        $event = Event::where('is_active', true)->firstOrFail();

        $registrant = Registrant::create(array_merge(
            $request->safe()->only([
                'name', 'email', 'mobile', 'tshirt_size', 'organisation', 'donation_amount',
            ]),
            [
                'event_id'       => $event->id,
                'category_id'    => $category->id,
                'qr_code'        => $this->qrCodes->unique(),
                'credit_balance' => $category->credits,
                'wallet_status'  => 'active',
                'goodie_status'  => 'pending',
                'pdpa_consent'   => true,
                'registered_at'  => now(),
            ]
        ));

        return response()->json($registrant->toArrayCreated(), 201);
    }
}
