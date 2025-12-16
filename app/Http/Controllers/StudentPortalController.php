<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentPortalController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register-lrn');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'lrn' => ['required', 'digits:12', 'exists:students,lrn'],
            'email' => ['nullable', 'email', 'unique:accounts,Email'],
            'username' => ['required', 'string', 'max:50', 'unique:accounts,Username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $student = Student::where('lrn', $data['lrn'])->firstOrFail();

        $account = Account::create([
            'Email' => $data['email'] ?? null,
            'Username' => $data['username'],
            'Password_Hash' => Hash::make($data['password']),
            'role' => 'student',
            'status' => 'pending',
        ]);

        $student->update(['account_ID' => $account->account_ID]);

        $role = Role::firstOrCreate(['role_name' => 'student_portal_readonly']);
        if (method_exists($account, 'roles')) {
            $account->roles()->syncWithoutDetaching([$role->getKey()]);
        }

        return redirect()->route('students.show', $student->student_ID)
            ->with('success', 'Your account will be available after admin activation');
    }
}
