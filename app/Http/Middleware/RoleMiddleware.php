<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()){
            return redirect()->route('login')->with('error', 'Anda belum login, silahkan login terlebih dahulu untuk menggunakan menu yang tersedia');
        }

        $user = auth()->user();

        // check jika user memiliki roles yang diperlukan
        foreach ($roles as $role) {
            if ($this->hasRole($user, $role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action. You do not have permission to access this page.');
    }

    /**
     * Check if user has the specified role.
     *
     * @param  \App\Models\User  $user
     * @param  string  $role
     * @return bool
     */
    protected function hasRole($user, string $role): bool
    {
        // Assuming you have linked_type field as role
        // You can customize this based on your database structure
        return strtolower($user->linked_type) === strtolower($role);
    }
}
