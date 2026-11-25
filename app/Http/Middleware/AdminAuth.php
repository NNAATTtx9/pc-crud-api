<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;
use App\Models\ApiToken;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $plain = $request->bearerToken();
        if (! $plain) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $token = ApiToken::find($plain);
        if (! $token) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        if ($token->owner_type !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $admin = Admin::find($token->owner_id);
        if (! $admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $request->attributes->set('admin', $admin);

        return $next($request);
    }
}
