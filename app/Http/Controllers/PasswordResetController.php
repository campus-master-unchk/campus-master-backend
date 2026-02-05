<?php

namespace App\Http\Controllers;

use App\Core\Domain\Entities\User;
use App\Mail\PasswordResetMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // dure de validite du token
    private $tokenExpirationMinutes = 30;

    /**
     * Envoi d'un lien de reinitialisation de mot de passe.
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email',
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
        }

        // creation d'un token
        $token = Str::random(60);

        // creation de la date d'expiration
        $expiredAt = now()->addMinutes($this->tokenExpirationMinutes);

        // supprimer si un token existe deja
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Sauvegarder le token
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'expired_at' => $expiredAt,
            'created_at' => Carbon::now()
        ]);

        // Générer l'URL de réinitialisation
        $resetUrl = $this->generateResetUrl($token, $user->email);

        // Envoyer l'email
        Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl, $this->tokenExpirationMinutes, $this->tokenExpirationMinutes));

        return response()->json([
            'status' => 'success',
            'message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.',
            'expires_in' => $this->tokenExpirationMinutes * 60, // en secondes
            'expires_at' => $expiredAt->toDateTimeString()
        ]);
    }


    /**
     * Reinitialisation du mot de passe.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier le token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token invalide ou expiré.'
            ], 400);
        }

        // Vérifier l'expiration
        if ($this->isTokenExpired($record)) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'status' => 'error',
                'message' => 'Le lien de réinitialisation a expiré.'
            ], 400);
        }

        // Vérifier le token
        if (!Hash::check($request->token, $record->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token invalide.'
            ], 400);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Invalider tous les tokens JWT de l'utilisateur
        $this->invalidateUserTokens($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'
        ]);
    }


    /**
     * Changer le mot de passe (pour utilisateur connecté)
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Non authentifié'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Le mot de passe actuel est incorrect.'
            ], 422);
        }

        // Vérifier que le nouveau mot de passe est différent
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Le nouveau mot de passe doit être différent de l\'actuel.'
            ], 422);
        }

        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->new_password);
        User::where('id', $user->id)->update([
            'password' => $user->password,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Mot de passe changé avec succès.',
        ]);
    }

    // fonction privee
    private function isTokenExpired($tokenRecord): bool
    {
        if (!$tokenRecord->expired_at) {
            // Si expired_at n'est pas défini, utiliser l'ancienne logique (24h)
            return Carbon::parse($tokenRecord->created_at)->addHours(24)->isPast();
        }

        return Carbon::now()->greaterThan(Carbon::parse($tokenRecord->expired_at));
    }

    /**
     * Invalider tous les tokens JWT de l'utilisateur
     */
    private function invalidateUserTokens(User $user)
    {
        try {
            $user->invalidateTokens();
        } catch (\Exception $e) {
            Log::error('Error invalidating user tokens: ' . $e->getMessage());
        }
    }

    /**
    * generation de l'URL de reinitialisation
    */
    private function generateResetUrl($token, $email)
    {
        return env('APP_URL_FRONT') . '/reset-password?token=' . $token . '&email=' . $email;
    }
}
