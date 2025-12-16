<?php

namespace App\Http\Controllers;

use App\Models\YearLevel;
use App\Models\YearLevelAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YearLevelController extends Controller
{
    public function index()
    {
        $levels = YearLevel::with(['assignments.teacher'])->orderBy('grade_level')->get();

        $summary = $levels->map(function ($yl) {
            $adviserCount = \App\Models\Section::where('grade_level', $yl->grade_level)
                ->where(function ($q) {
                    $q->whereNotNull('teacher_ID')
                        ->orWhereHas('teachers');
                })->count();
            $pendingCount = $yl->assignments->where('status', 'pending')->count();

            return [
                'id' => $yl->year_level_ID,
                'grade' => $yl->grade_level,
                'students' => $yl->student_count,
                'approvedTeachers' => $adviserCount,
                'pendingAssignments' => $pendingCount,
            ];
        });

        return view('year_levels.index', compact('levels', 'summary'));
    }

    public function show(YearLevel $year_level)
    {
        $year_level->load(['assignments.teacher']);
        $sections = \App\Models\Section::where('grade_level', $year_level->grade_level)
            ->with(['teacher', 'teachers', 'students'])
            ->orderBy('section_name')
            ->get();

        $totalStudents = (int) \App\Models\Student::whereHas('section', function ($q) use ($year_level) {
            $q->where('grade_level', $year_level->grade_level);
        })->count();

        $approved = $year_level->assignments->where('status', 'approved');
        $pending = $year_level->assignments->where('status', 'pending');

        return view('year_levels.show', compact('year_level', 'sections', 'totalStudents', 'approved', 'pending'));
    }

    public function requestAssignment(Request $request, YearLevel $year_level)
    {
        $this->authorize('create', YearLevelAssignment::class);

        $teacherId = Auth::user()?->teacher->teacher_ID ?? null;
        if (! $teacherId) {
            return back()->withErrors(['assignment' => 'Only teachers can request assignments.']);
        }

        YearLevelAssignment::create([
            'year_level_ID' => $year_level->year_level_ID,
            'teacher_ID' => $teacherId,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return back()->with('success', 'Assignment request submitted');
    }

    public function approve(Request $request, YearLevelAssignment $assignment)
    {
        $this->authorize('update', $assignment);
        $assignment->update([
            'status' => 'approved',
            'approved_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'approved_at' => now(),
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Assignment approved');
    }

    public function reject(Request $request, YearLevelAssignment $assignment)
    {
        $this->authorize('update', $assignment);
        $assignment->update([
            'status' => 'rejected',
            'approved_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'approved_at' => now(),
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Assignment rejected');
    }
}
