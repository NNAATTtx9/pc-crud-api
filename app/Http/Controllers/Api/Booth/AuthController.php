<?php

namespace App\Http\Controllers\Api\Booth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoothStaff;
use App\Models\ApiToken;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request -> validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $booth = BoothStaff::where('username', $data['username'])->first();

        if (
            ! $booth
            || ! $booth->is_active
            || ! Hash::check($data['password'], $booth->password)
        ) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        return response()->json([
            'token' => ApiToken::issue('booth', $booth->id),
            'booth' => $booth->only(['id', 'username', 'booth_type']),
        ], 200);
    }
}
