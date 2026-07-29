<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordSetupController extends Controller
{
    public function create()
    {
        return view('auth.setup-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
            'force_password_change' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Account activated! Welcome to your dashboard.');
    }
}