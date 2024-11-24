<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
        $token = $request->bearerToken();

        $accessToken = PersonalAccessToken::findToken($token);

        if(!$accesToken || $accessToken->expires_at < now()){
            return response()->json(['message' => 'Token expired. Please login again.'], 401);
        }
        return $next($request);
        
    }
}
