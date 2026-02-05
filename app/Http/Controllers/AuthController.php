<?php

namespace App\Http\Controllers;

use App\Core\Domain\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');


        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email ou mot de passe incorrect',
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 720, // en secondes

            ]
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        return response()->json(
            [
                'status' => 'success',
                'user' => Auth::user(),
            ]
        );
    }

    /**
     * Update my profile
     */
    public function updateMyProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        User::where('id', $user->id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        return response()->json(
            [
                'status' => 'success',
                'user' => $user,
            ]
        );
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Déconnexion reussie',
        ]);
    }
}
