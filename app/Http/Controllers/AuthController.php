<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Login Form Show karna
    public function showLogin()
    {
        \App\Services\FirebaseService::ensureDatabaseSeeded();
        return view('auth.login');
    }

    // Login Process
    public function login(Request $request)
    {
        \App\Services\FirebaseService::ensureDatabaseSeeded();

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Role ke mutabiq redirection
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/dashboard');
            } else {
                return redirect()->intended('/pos');
            }
        }

        // Retry after syncing from Firebase
        \App\Services\FirebaseService::syncFirebaseUsersToLocal();
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/dashboard');
            } else {
                return redirect()->intended('/pos');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Register Form Show karna
    public function showRegister()
    {
        return view('auth.register');
    }

    // Register Process
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,cashier',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect('/dashboard');
        } else {
            return redirect('/pos');
        }
    }

    // Firebase Google Auth Login
    public function firebaseLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string',
            'uid' => 'nullable|string',
        ]);

        $email = $request->email;
        $name = $request->name ?: explode('@', $email)[0];

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => \Illuminate\Support\Str::random(16),
                'role' => 'admin',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $redirectUrl = ($user->role === 'admin') ? '/dashboard' : '/pos';

        return response()->json([
            'success' => true,
            'redirect' => $redirectUrl,
        ]);
    }

    // Logout Process
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // Show Forgot Password View
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Process Forgot Password Email Verification
    public function processForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        \App\Services\FirebaseService::ensureDatabaseSeeded();

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No registered account found with this email address.',
            ])->withInput();
        }

        return redirect()->route('password.reset', ['email' => $user->email])
            ->with('status', 'Account verified! Please set your new password.');
    }

    // Show Reset Password View
    public function showResetPassword(Request $request, $email = null)
    {
        $email = $email ?: $request->query('email');
        return view('auth.reset-password', compact('email'));
    }

    // Process Password Reset
    public function processResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        \App\Services\FirebaseService::ensureDatabaseSeeded();

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'User not found.',
            ])->withInput();
        }

        $user->password = $request->password;
        $user->save();

        return redirect()->route('login')->with('message', 'Password updated successfully! Please login with your new password.');
    }

    /**
     * Get current user's active role (for real-time status check).
     */
    public function userRoleStatus()
    {
        if (!Auth::check()) {
            return response()->json(['authenticated' => false], 401);
        }

        $user = Auth::user()->fresh();

        return response()->json([
            'authenticated' => true,
            'role' => $user->role ?? 'user',
            'redirect' => ($user->role === 'admin' || $user->role === 'pharmacist') ? '/dashboard' : '/pos',
        ]);
    }
}