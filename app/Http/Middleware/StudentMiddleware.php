<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Non authentifié'
            ], 401);
        }
        
        if (Auth::user()->user_type !== 'student') {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès réservé aux étudiants'
            ], 403);
        }

        return $next($request);
    }
}

?>