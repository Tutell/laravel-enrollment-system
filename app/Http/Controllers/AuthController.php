<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['nullable', 'email'],
            'username' => ['nullable', 'string'],
            'password' => ['required', 'string'],
        ]);

        $account = null;
        if (! empty($data['email'])) {
            $account = Account::where('Email', $data['email'])->first();
        } elseif (! empty($data['username'])) {
            $account = Account::where('Username', $data['username'])->first();
        }

        if (! $account || ! Hash::check($data['password'], $account->Password_Hash)) {
            return back()->withErrors(['login' => 'Invalid credentials'])->withInput();
        }

        if (($account->status ?? 'active') !== 'active') {
            return back()->withErrors(['login' => 'Your account is not active. Please wait for admin activation.'])->withInput();
        }

        $remember = (bool) $request->boolean('remember');
        Auth::login($account, $remember);
        $request->session()->regenerate();
        if ($account->role === 'teacher') {
            try {
                $teacher = \App\Models\Teacher::where('account_ID', $account->account_ID)->first();
                \App\Models\TeacherAccessLog::create([
                    'account_ID' => $account->account_ID,
                    'teacher_ID' => optional($teacher)->teacher_ID,
                    'action' => 'Login',
                    'ip_address' => $request->ip(),
                ]);
            } catch (\Throwable $e) {
                // swallow logging errors
            }
        }
        if (strtolower($account->role ?? '') === 'student') {
            return redirect()->intended(route('student.dashboard'));
        }

        return redirect()->intended(route('dashboard.index'));
    }

    // Public register disabled. Use admin Manage Accounts.

    public function logout()
    {
        $account = Auth::user();
        $ip = request()->ip();
        Auth::logout();
        if ($account && $account->role === 'teacher') {
            try {
                $teacher = \App\Models\Teacher::where('account_ID', $account->account_ID)->first();
                \App\Models\TeacherAccessLog::create([
                    'account_ID' => $account->account_ID,
                    'teacher_ID' => optional($teacher)->teacher_ID,
                    'action' => 'Logout',
                    'ip_address' => $ip,
                ]);
            } catch (\Throwable $e) {
            }
        }

        return redirect()->route('login');
    }
}
