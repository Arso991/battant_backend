<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{

    //inscription
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->all();
            //créer un utilisateur
            $user = User::create([
                'lastname' => isset($request->lastname) ? $request->lastname : null,
                'firstname' => isset($request->firstname) ? $request->firstname : null,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            $user->assignRole($request->role);

            $vericationUrl = URL::temporarySignedRoute('verifyEmail', Carbon::now()->addMinute(60), ['id' => $user->id, 'hash' => sha1($request->email)]);

            //Mail::to($user['email'], $user['firstname'])->send(new VerifyEmail($vericationUrl));

            Mail::send('mailConfirm', ['verificationUrl' => $vericationUrl, 'name' => isset($data['firstname']) ? $data['firstname'] : explode('@', $data['email'])[0]], function ($message) use ($data) {
                $config = config('mail');
                $message->subject('Verification de votre mail')
                    ->from($config['from']['address'], $config['from']['name'])
                    ->to($data['email'], isset($data['firstname']) ? $data['firstname'] : explode('@', $data['email'])[0]);
            });

            $token = $user->createToken('auth_token')->plainTextToken;


            return response()->json([
                'message' => 'User successfully registered, verify your mail for confirmation',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 60 * 60 // 60 minutes
            ], 201);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    //verification de mail
    public function verify(Request $request, $id, $hash)
    {
        try {
            //code...
            $user = User::find($id);

            if (!hash_equals((string) $hash, sha1($user['email']))) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if (!$request->hasValidSignature()) {
                return response()->json(['message' => 'Verication link has expired ! Please click resend link.'], 404);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Email already verified'], 200);
            }

            $user->update([
                'email_verified' => true,
                'email_verified_at' => Carbon::now(),
            ]);

            return response()->json(['message' => 'Email verified successfully.'], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    //renvoie du mail de confirmation
    public function emailResend(Request $request)
    {
        try {
            //code...
            $user = $request->user();

            $vericationUrl = URL::temporarySignedRoute('verifyEmail', Carbon::now()->addMinute(60), ['id' => $user->id, 'hash' => sha1($request->email)]);

            Mail::send(
                'mailConfirm',
                ['verificationUrl' => $vericationUrl, 'name' => $user->firstname],
                function ($message) use ($user) {
                    $config = config('mail');
                    $message->subject('Verification de votre mail')
                        ->from($config['from']['address'], $config['from']['name'])
                        ->to($user['email'], $user['firstname']);
                }
            );
            return response()->json(['message' => 'Verification link sent!']);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    //connexion
    public function login(LoginRequest $request)
    {
        try {
            //code...
            $credentials = Auth::attempt(['email' => $request->email, 'password' => $request->password, "email_verified" => true]);

            if ($credentials) {
                $user = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'data' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 60 * 60 // 60 minutes
                ]);
            }

            return response()->json(['message' => 'Unauthorised'], 401);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    // connexion par reseaux soxiaux
    public function socialLogin(Request $request)
    {
        $provider = $request->provider;

        if (!in_array($provider, ['facebook', 'google', 'twitter', 'instagram'])) {
            return response()->json(['error' => 'Invalid provider'], 400);
        }

        $socialUser = Socialite::driver($provider)->userFromToken($request->token);

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'first_name' => $socialUser->getName(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'role' => 'client',
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    //mot de passe oublié
    public function forgotPassword(Request $request)
    {
        try {
            $data = $request->all();
            $request->validate(['email' => 'required|email']);

            // Envoi du lien de réinitialisation
            $user = User::where('email', $request->email);

            if (!$user) {
                return response()->json(['message' => 'You don\'t have an account with this email']);
            }
            $resetLink = url('/reset-password/' . sha1($request->email));
            Mail::send(
                'mailReset',
                ['resetLink' => $resetLink, 'name' => explode('@', $data['email'])[0]],
                function ($message) use ($data) {
                    $config = config('mail');
                    $message->subject('Réinitialisation de mot de passe')
                        ->from($config['from']['address'], $config['from']['name'])
                        ->to($data['email']);
                }
            );

            return response()->json(['message' => 'Reset link sent to your email.']);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    //reset password
    public function resetPassword(Request $request, $hash)
    {
        try {
            //code...
            $request->validate([
                //'token' => 'required|string',
                'password' => 'required|string|min:4|confirmed',
            ]);

            $user = User::whereRaw('SHA1(email) = ?', $hash);

            if ($user) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
                return response()->json(['message' => 'Password reset successfully.'], 200);
            }

            return response()->json(['message' => 'Failed to reset password.'], 400);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    // change password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:4|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    //deconnexion
    public function logout()
    {
        try {
            //supprimer le token à la déconnexion
            Auth::user()->tokens()->delete();
            //auth()->user()->tokens()->delete();

            return response()->json([
                "message" => "User logged out"
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
