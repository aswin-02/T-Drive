<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminAuthController extends Controller
{
    // Show login form (handled by route closure)

    // Handle admin login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Only allow users with is_admin = true
        $admin = User::where('email', $request->email)->where('user_type','admin')->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::login($admin);
            return redirect()->route('dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials or not an admin.'])->withInput();
    }

    // Handle admin logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
