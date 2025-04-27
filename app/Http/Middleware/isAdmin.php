<?php

namespace App\Http\Middleware;

use App\Enum\UserType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (Auth::check() && $user->usertype === UserType::Admin->value) {
            return $next($request);
        }
        return response()->json([
            'message' => 'UnAuthorized Action',
        ], Response::HTTP_UNAUTHORIZED);

    }
}
