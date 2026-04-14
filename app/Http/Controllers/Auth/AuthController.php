<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\DTOs\Auth\LoginDTO;
use App\Services\Auth\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Exceptions\Auth\UserInactiveException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $user = $this->authService->login(
                LoginDTO::fromRequest($request->validated())
            );

            $request->session()->regenerate();

            return redirect()->intended(
                route('dashboard')
            );

        } catch (UserInactiveException|InvalidCredentialsException $e) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => $e->getMessage()]);
        }
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}
