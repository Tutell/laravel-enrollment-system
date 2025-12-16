<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $account = Auth::user();
        if (! $account || strtolower($account->role ?? '') !== 'student') {
            abort(403);
        }

        $student = $account->student;
        if (! $student) {
            return redirect()->route('portal.register-lrn')->withErrors([
                'student' => 'No student record linked to this account. Please register via LRN.',
            ]);
        }

        $student->load(['section', 'grades', 'enrollments.course.subject', 'enrollments.course.teacher']);

        $gradeLevel = optional($student->section)->grade_level;
        $sections = collect();
        if ($gradeLevel) {
            $sections = Section::where('grade_level', $gradeLevel)
                ->with(['teacher'])
                ->orderBy('section_name')
                ->get();
        }

        $courses = $student->enrollments->map->course->filter();
        $subjects = $courses->map->subject->filter()->unique('subject_id');
        $gradeSubjects = collect();
        if (is_numeric($gradeLevel) && $gradeLevel >= 7 && $gradeLevel <= 12) {
            $gradeSubjects = Subject::where('grade_level', (int) $gradeLevel)
                ->orderBy('name')
                ->get();
        }
        $grades = $student->grades;

        $announcements = [];

        return view('student.dashboard', compact(
            'student',
            'gradeLevel',
            'sections',
            'courses',
            'subjects',
            'gradeSubjects',
            'grades',
            'announcements'
        ));
    }
}
