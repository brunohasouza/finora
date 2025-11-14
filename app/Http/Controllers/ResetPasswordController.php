<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class ResetPasswordController extends Controller
{
    public function resetPasswordPage(Request $request, String $token)
    {
        return Inertia::render('Auth/ResetPasswordPage', [ 
            'token' => $token,  
            'email' => $request->email 
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PasswordReset
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }    
}
