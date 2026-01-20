<?php

namespace App\Http\Controllers;

use App\Mail\SuccessRegisterMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    private function generatePassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

     /**
     * Register a new user
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_type' => 'required|in:student,teacher',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $password = $this->generatePassword();

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_type' => $request->user_type,
            'email' => $request->email,
            'password' => Hash::make($password),
        ]);

        // Send email with identifiants
        Mail::to($request->email)->send(new SuccessRegisterMail(
            $request->first_name,
            $request->last_name,
            $request->email,
            $password,
            env('APP_URL_FRONT')
        ));


        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur créé avec succès',
        ], 201);
    }

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
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        User::where('id', $user->id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
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