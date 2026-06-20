<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\UserType;

class CheckRole
{
    public function handle(Request $request, Closure $next, $roles)
    {
        $roles = is_array($roles) ? $roles : explode(',', $roles);
        $userRole = auth()->user()->type->value; // Get the value of the enum

        if (!auth()->check() || !in_array($userRole, $roles)) {
            return redirect()->route('home')->with('error', 'You do not have access to this section.');
        }

        return $next($request);
    }

}
