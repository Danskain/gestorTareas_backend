<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $rol)
    {
        $user = JWTAuth::parseToken()->authenticate();
        //dd($rol);
        if (!$user || $user->rol !== $rol) {
            //dd($user->rol, $user->name, $rol);
            return response()->json(['error' => 'Rol No autorizado'], 403);
        }

        return $next($request);
    }
}
