<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // Tangani token kedaluwarsa
            return response()->json(['error' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            // Tangani token tidak valid
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            // Tangani kesalahan lainnya yang terkait dengan JWT
            return response()->json(['error' => 'Failed to authenticate token'], 500);
        }

        return $next($request);
    }
}
