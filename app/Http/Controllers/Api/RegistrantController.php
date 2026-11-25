<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registrant;

class RegistrantController extends Controller
{
    public function show($id)
    {
        $registrant = Registrant::with('category')->find($id);
        if (! $registrant) {
            return response()->json(['error' => 'Registrant not found'], 404);
        }
        return response()->json($registrant->toArrayPublic(), 200);
    }
}
