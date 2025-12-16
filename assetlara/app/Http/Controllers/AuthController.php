<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;


class AuthController extends Controller
{
    // Dependency Injection: Laravel gives us the Service instance
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Show the login form.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. The Request automatically validated the input before reaching here.
        // 2. We pass the validated data to the Service.
        $this->authService->authenticate(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );
        // Redirect based on role
        if (Auth::user()->role === 'admin') {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended(route('my-assets'));
    }

    /**
     * Handle logout.
     */
    public function destroy(): RedirectResponse
    {
        $this->authService->logout();
        return redirect()->route('login');
    }

    /**
     * Show the registration form.
     */
    public function createRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle the registration request.
     */
    public function storeRegister(RegisterRequest $request): RedirectResponse
    {
        // 1. The Request automatically validated the input.
        // 2. We pass the validated data to the Service to handle registration and login.
        $user = $this->authService->register($request->validated());

        // 3. Handle the web-specific login (session)
        /** @var \App\Models\User $user */
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect to employee dashboard
        return redirect()->route('my-assets')
            ->with('success', 'Registration successful! Welcome.');
    }
}
