<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    public function forgotPasswordPage()
    {
        return Inertia::render('Auth/ForgotPasswordPage', ['status' => session('status')]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }
}
