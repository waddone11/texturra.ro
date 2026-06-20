<?php

namespace App\Http\Middleware;
use Closure;
class VerifySecretKey
{
    public function handle($request, Closure $next)
    {
        if ($request->input('key') !== config('app.secret_key')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}

