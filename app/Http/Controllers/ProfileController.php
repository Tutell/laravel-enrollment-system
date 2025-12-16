<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $account = Auth::user();
        if (! $account) {
            abort(403);
        }

        $student = $account->student;
        if (! $student) {
            return redirect()->route('portal.register-lrn')->withErrors([
                'student' => 'No student record linked to this account. Please register via LRN.',
            ]);
        }

        $student->load([
            'account',
            'section',
            'guardians',
            'enrollments.course.subject',
            'enrollments.course.teacher',
        ]);

        return view('profile.show', [
            'account' => $account,
            'student' => $student,
        ]);
    }
}
