<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\PasswordOtp;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    private function getFrontendUrl() {
        return rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(Str::random(24)),
                ]);
            } else {
                $user->update([
                    'google_id' => $user->google_id ?? $googleUser->id,
                    'avatar' => $user->avatar ?? $googleUser->avatar,
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            $frontendUrl = $this->getFrontendUrl();

            return redirect("{$frontendUrl}/auth/callback?token=" . urlencode($token));
        } catch (\Exception $e) {
            return redirect($this->getFrontendUrl() . "/signup?error=google_failed");
        }
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), 
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Login failed',
                'errors' => ['email' => ['Invalid email or password.']]
            ], 422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $token,
        ], 200);
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = (string) random_int(100000, 999999);

        PasswordOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(15)]
        );

        try {
            Mail::to($request->email)->send(new OtpMail($otp));
            return response()->json(['message' => 'OTP sent to your email.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email.'], 500);
        }
    }

    public function resetPasswordByOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $otpRecord = PasswordOtp::where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->password = Hash::make($validated['password']); 
        $user->save();

        $otpRecord->delete();

        return response()->json(['message' => 'Password reset successfully.']);
    }

    public function convertGuestToMember(Request $request)
    {
        $validated = $request->validate([
            'order_id'  => 'required|exists:orders,id',
            'password'  => 'required|string|min:8',
        ]);

        $order = \App\Models\Order::findOrFail($validated['order_id']);

        if (empty($order->email)) {
            return response()->json(['message' => 'Order does not have an email associated with it.'], 422);
        }

        if (\App\Models\User::where('email', $order->email)->exists()) {
            return response()->json(['message' => 'An account already exists for this email.'], 422);
        }

        // If the name is missing, fall back to using email as name
        $user = \App\Models\User::create([
            'name'     => $order->full_name ?: $order->email,
            'email'    => $order->email,
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        $order->update(['user_id' => $user->id]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'         => $user->only(['id', 'name', 'email']),
            'access_token' => $token,
            'message'      => 'Welcome! Your account is ready.',
        ]);
    }
}