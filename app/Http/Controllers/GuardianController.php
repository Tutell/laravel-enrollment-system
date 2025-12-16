<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    public function create(Student $student)
    {
        return view('guardians.create', compact('student'));
    }

    public function store(Request $request, Student $student)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'relationship' => ['required', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:100'],
        ]);

        Guardian::create($data + ['student_ID' => $student->student_ID]);

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Guardian information saved');
    }
}
