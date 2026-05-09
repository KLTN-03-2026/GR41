<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $rolesCsv): Response
    {
        $roles = array_values(array_filter(array_map('trim', explode(',', $rolesCsv))));
        $user = $request->user()?->loadMissing('role');
        if (! $user || ! $user->role || ! in_array($user->role->slug, $roles, true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
