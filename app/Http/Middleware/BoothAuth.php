<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiToken;
use App\Models\BoothStaff;

class BoothAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $plain = $request->bearerToken();
        if (! $plain) {
            return response()->json(["error" => "Unauthenticated"], 401);
        }
        $token = ApiToken::find($plain);
        if (! $token) {
            return response()->json(["error" => "Unauthenticated"], 401);
        }
        if ($token->owner_type !== 'booth') {
            return response()->json(['error' => "Forbidden"], 403);
        }
        $booth = BoothStaff::find($token->owner_id);
        if (! $booth || ! $booth->is_active) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $request->attributes->set('booth', $booth);

        return $next($request);
    }
}
