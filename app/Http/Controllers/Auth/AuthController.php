<?php
// app/Http/Controllers/Auth/AuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is active
            if ($user->status != 'active') {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is inactive. Please contact administrator.'
                ]);
            }

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();
            
            // Log the activity
            if (class_exists('\App\Services\ActivityLogService')) {
                ActivityLogService::logAction('login', "User logged in: {$user->name}");
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => route('admin.dashboard')
            ]);
        }

        // Don't redirect on failure, just return error
        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password. Please try again.'
        ]);
    }

    public function showRegistrationForm()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ]);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 1, // Regular user
                'status' => 'active',
            ]);

            // Login the user
        // Login the user
Auth::login($user);

// Regenerate session
$request->session()->regenerate();

// Log the activity using the existing log method
if (class_exists('\App\Services\ActivityLogService')) {
    ActivityLogService::log('registered', $user, "User registered: {$user->name}");
}

return response()->json([
    'success' => true,
    'message' => 'Registration successful! Welcome!',
    'redirect' => route('admin.dashboard')
]);

            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ]);
        }
    }

    public function logout(Request $request)
{
    $user = Auth::user();
    
    // Log activity before logout (using the log method with a dummy model)
    if ($user && class_exists('\App\Services\ActivityLogService')) {
        // Use the existing log method with the user as the model
        ActivityLogService::log('logout', $user, "User logged out: {$user->name}");
    }
    
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}

}
