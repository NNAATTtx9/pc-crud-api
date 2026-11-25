<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\ApiToken;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' =>'required|string',
        ]);

        $admin = Admin::where('username', $data['username'])->first();

        if (! $admin || ! Hash::check($data['password'], $admin->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        return response()->json([
            'token' => ApiToken::issue('admin', $admin->id),
            'admin' => $admin->only(['id', 'username', 'role']),
        ], 200);
    }
}
