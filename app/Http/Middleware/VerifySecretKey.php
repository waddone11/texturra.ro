<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the /commands deploy helper. Requires a secret matching
 * config('commands.secret') via the X-Command-Secret header OR ?secret= query.
 * Missing config OR wrong key → 404 (do not reveal the route exists).
 */
class VerifySecretKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('commands.secret');
        $provided = $request->header('X-Command-Secret') ?? $request->query('secret');

        // Gate closed by default: no configured secret → 404.
        if (! is_string($secret) || $secret === '') {
            abort(404);
        }

        if (! is_string($provided) || ! hash_equals($secret, $provided)) {
            abort(404);
        }

        return $next($request);
    }
}
