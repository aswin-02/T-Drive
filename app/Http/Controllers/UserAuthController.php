<?php

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Http\Requests\User\SignupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserAuthController extends Controller
{
    public function signup(SignupRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

        $referred_by = null;
        if (!empty($validated['referral_code'])) {
                $referrer = User::where('invite_code', $validated['referral_code'])->where('user_type', 'user')->first();
                $referred_by = $referrer->id;
            }

        // Always generate a unique 8-character uppercase invite_code for each customer
        do {
            $invite_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (User::where('invite_code', $invite_code)->exists());

            $user = User::create([
                'name' => $validated['name'],
                'mobile' => $validated['mobile'],
                'password' => Hash::make($validated['password']),
                'password1' => EncryptDecrypt("encrypt",$validated['password']),
                'invite_code' => $invite_code,
                'referred_by' => $referred_by,
                'user_type' => 'user',
            ]);
            DB::commit();
            
            Logger::log('create', $user, null, $user->toArray());

            Auth::login($user);
            return redirect('/home');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->getCode() == 23000) {
                // Duplicate entry error - check which field
                if (str_contains($e->getMessage(), 'mobile')) {
                    return back()->withErrors(['mobile' => 'Mobile number already exists.'])->withInput();
                } else if (str_contains($e->getMessage(), 'email')) {
                    return back()->withErrors(['email' => 'Email already exists.'])->withInput();
                }
            }
            return back()->withErrors(['mobile' => 'An error occurred while creating the account.'])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['mobile' => 'Unexpected error: ' . $e->getMessage()])->withInput();
        }
    }
    public function signin(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['required'],
            'password' => ['required']
        ]);

        $user = User::where('mobile', $validated['mobile'])->where('user_type','user')->first();

        if (!$user) {
            return back()->withErrors(['mobile' => 'Mobile number does not exist'])->withInput();
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password'])->withInput();
        }
        
        Auth::login($user);
        Logger::log('login', $user, null, null);
        
        return redirect('/home');
    }
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Logger::log('logout', $user, null, null);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.signin');
    }
    public function showSignIn(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        return view('user.signin');
    }

    public function showSignUp(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        return view('user.signup');
    }
}
