<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
    $googleUser = Socialite::driver('google')->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'User'),
                // Random password to satisfy not-null constraint; not used for login
                'password' => Hash::make(Str::random(32)),
                'user_type' => 'user',
            ]
        );

        // If existing user without user_type, ensure default
        if (!$user->user_type) {
            $user->user_type = 'user';
            $user->save();
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
