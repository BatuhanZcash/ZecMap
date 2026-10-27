<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicLoginRequest;
use App\Http\Requests\PublicRegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth');
    }

    public function store(PublicLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        auth()->user()?->recordSignIn();

        return redirect()->intended(route('browse'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('browse');
    }

    public function registerCreate(): View
    {
        return view('register');
    }

    public function registerStore(PublicRegisterRequest $request): RedirectResponse
    {
        $username = (string) $request->input('username');

        $user = User::query()->create([
            'username' => $username,
            'name' => $username,
            'email' => $username.'@example.com',
            'password' => $request->input('password'),
            'role' => User::ROLE_USER,
            'email_verified_at' => now(),
            'last_sign_in_at' => now(),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('browse'));
    }
}
