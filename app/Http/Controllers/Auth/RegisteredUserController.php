<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate inputs (Parent fields are nullable in case they skip it)
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'grade_level' => ['required', 'string'],
            
            'parent_first_name' => ['nullable', 'string', 'max:255'],
            'parent_last_name' => ['nullable', 'string', 'max:255'],
            'parent_email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
        ]);

        // 2. Create the Student Account FIRST
        $student = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        // 3. If the toggle was checked, create the Parent and send SMS
        if ($request->has('has_parent') || $request->has_parent) {
            $activationCode = random_int(100000, 999999);
            
            $parent = User::create([
                'first_name' => $request->parent_first_name,
                'last_name' => $request->parent_last_name,
                'email' => $request->parent_email,
                'contact_number' => $request->parent_phone,
                'role' => 'parent',
                'password' => Hash::make($activationCode),
                'force_password_change' => true,
            ]);
            
            // Link the parent to the student and sync the mobile number
            $student->update([
                'parent_id' => $parent->id, 
                'contact_number' => $parent->contact_number
            ]);

            // Send Activation SMS
            $smsMsg = "PROGNOSMATH\nUsername: {$parent->email}\nCode: {$activationCode}\nLogin using this code to activate your parent account and set a secure password.";
            app(\App\Services\PhilSmsService::class)->sendSms($parent->contact_number, $smsMsg);
        }

        // 4. Log the student in and redirect to their dashboard
        event(new Registered($student));
        Auth::login($student);

        return redirect()->route('dashboard')->with('success', 'Account created! If you added a parent, their activation code has been sent via SMS.');
    }
}