<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowed = false;
        $roles = array_map(function ($r) {
            return strtolower((string) $r);
        }, $roles);

        if ($user->role && in_array(strtolower($user->role), $roles, true)) {
            $allowed = true;
        }

        if (! $allowed && method_exists($user, 'roles') && $user->roles instanceof \Illuminate\Support\Collection) {
            $userRoleNames = array_map('strtolower', $user->roles->pluck('role_name')->all());
            foreach ($roles as $role) {
                if (in_array($role, $userRoleNames, true)) {
                    $allowed = true;
                    break;
                }
            }
        }

        if (! $allowed) {
            abort(403);
        }

        return $next($request);
    }
}
