<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradeRequest;
use App\Models\Grade;
use App\Models\EnrollmentAudit;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\YearLevelAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with(['enrollment.student', 'enrollment.course'])->paginate(20);

        return view('grades.index', compact('grades'));
    }

    public function store(StoreGradeRequest $request)
    {
        $data = $request->validated();
        if (!$this->canModifyEnrollment(\Illuminate\Support\Facades\Auth::user(), $data['enrollment_id'])) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Permission denied'], 403);
            }
            return redirect()->back()->withErrors(['permission' => 'You are not allowed to modify this grade.']);
        }
        $grade = Grade::create($data);
        try {
            EnrollmentAudit::create([
                'enrollment_ID' => $grade->enrollment_id,
                'processed_by_account_ID' => optional(\Illuminate\Support\Facades\Auth::user())->getAuthIdentifier(),
                'action' => 'grade_created',
                'changes' => json_encode($data),
            ]);
        } catch (\Throwable $e) {}

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'grade_id' => $grade->getKey()]);
        }
        return redirect()->back()->with('success', 'Grade recorded');
    }

    public function update(StoreGradeRequest $request, Grade $grade)
    {
        if (!$this->canModifyEnrollment(\Illuminate\Support\Facades\Auth::user(), $grade->enrollment_id)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Permission denied'], 403);
            }
            return redirect()->back()->withErrors(['permission' => 'You are not allowed to modify this grade.']);
        }
        $data = $request->validated();
        $changes = [];
        foreach ($data as $k => $v) {
            if ($grade->{$k} != $v) {
                $changes[$k] = ['old' => $grade->{$k}, 'new' => $v];
            }
        }
        $grade->update($data);
        try {
            if (!empty($changes)) {
                EnrollmentAudit::create([
                    'enrollment_ID' => $grade->enrollment_id,
                    'processed_by_account_ID' => optional(\Illuminate\Support\Facades\Auth::user())->getAuthIdentifier(),
                    'action' => 'grade_updated',
                    'changes' => json_encode($changes),
                ]);
            }
        } catch (\Throwable $e) {}

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok']);
        }
        return redirect()->back()->with('success', 'Grade updated');
    }

    public function destroy(Grade $grade)
    {
        $changes = ['grade_id' => $grade->getKey(), 'enrollment_id' => $grade->enrollment_id, 'type' => $grade->type];
        try {
            EnrollmentAudit::create([
                'enrollment_ID' => $grade->enrollment_id,
                'processed_by_account_ID' => optional(\Illuminate\Support\Facades\Auth::user())->getAuthIdentifier(),
                'action' => 'grade_deleted',
                'changes' => json_encode($changes),
            ]);
        } catch (\Throwable $e) {}
        $grade->delete();

        return redirect()->back()->with('success', 'Grade removed');
    }

    protected function canModifyEnrollment($account, $enrollmentId)
    {
        $role = strtolower(optional($account)->role ?? '');
        if ($role === 'admin') return true;
        if ($role !== 'teacher') return false;
        $teacher = \App\Models\Teacher::where('account_ID', $account->account_ID)->first();
        if (!$teacher) return false;
        $allowedSectionIds = DB::table('section_teacher')
            ->where('teacher_ID', $teacher->teacher_ID)
            ->pluck('section_ID')
            ->merge(
                Section::where('teacher_ID', $teacher->teacher_ID)->pluck('section_ID')
            )
            ->unique()
            ->values()
            ->toArray();
        $allowedGradeLevels = YearLevelAssignment::where('teacher_ID', $teacher->teacher_ID)
            ->where('year_level_assignments.status', 'approved')
            ->join('year_levels', 'year_levels.year_level_ID', '=', 'year_level_assignments.year_level_ID')
            ->pluck('year_levels.grade_level')
            ->unique()
            ->values()
            ->toArray();
        $enr = Enrollment::with('student.section')->where('enrollment_ID', $enrollmentId)->first();
        if (!$enr || !$enr->student) return false;
        $sid = optional($enr->student)->section_ID;
        $gl = optional(optional($enr->student)->section)->grade_level;
        if ($sid && in_array($sid, $allowedSectionIds)) return true;
        if ($gl && in_array($gl, $allowedGradeLevels)) return true;
        return false;
    }

    public function manage(Request $request)
    {
        $account = auth()->user();
        $role = strtolower($account->role ?? '');
        $teacher = null;
        $allowedSectionIds = [];
        $allowedGradeLevels = [];
        $academicYearFilter = (int) $request->query('academic_year_id', 0);
        $semesterFilter = $request->query('semester');
        if ($role === 'teacher') {
            $teacher = \App\Models\Teacher::where('account_ID', $account->account_ID)->first();
            if ($teacher) {
                $allowedSectionIds = \Illuminate\Support\Facades\DB::table('section_teacher')
                    ->where('teacher_ID', $teacher->teacher_ID)
                    ->pluck('section_ID')
                    ->merge(
                        \App\Models\Section::where('teacher_ID', $teacher->teacher_ID)->pluck('section_ID')
                    )
                    ->unique()
                    ->values()
                    ->toArray();
                $allowedGradeLevels = \App\Models\YearLevelAssignment::where('teacher_ID', $teacher->teacher_ID)
                    ->where('year_level_assignments.status', 'approved')
                    ->join('year_levels', 'year_levels.year_level_ID', '=', 'year_level_assignments.year_level_ID')
                    ->pluck('year_levels.grade_level')
                    ->unique()
                    ->values()
                    ->toArray();
            }
        }

        $gradeFilter = (int) $request->query('grade_level', 0);
        $sectionFilter = (int) $request->query('section_id', 0);
        $subjectFilter = (int) $request->query('subject_id', 0);

        $gradeLevels = \App\Models\Section::distinct()->orderBy('grade_level')->pluck('grade_level')->unique()->toArray();
        $sectionsQuery = \App\Models\Section::with(['students' => function ($q) {
            $q->with(['account', 'enrollments.course.subject', 'grades']);
        }]);
        if ($gradeFilter) {
            $sectionsQuery->where('grade_level', $gradeFilter);
        }
        if ($sectionFilter) {
            $sectionsQuery->where('section_ID', $sectionFilter);
        }
        if ($role === 'teacher') {
            $sectionsQuery->where(function ($q) use ($allowedSectionIds, $allowedGradeLevels, $gradeFilter) {
                if (!empty($allowedSectionIds)) {
                    $q->whereIn('section_ID', $allowedSectionIds);
                } elseif (!empty($allowedGradeLevels)) {
                    $q->whereIn('grade_level', $allowedGradeLevels);
                    if ($gradeFilter) {
                        $q->where('grade_level', $gradeFilter);
                    }
                } else {
                    $q->whereRaw('1=0');
                }
            });
        }
        $sections = $sectionsQuery->orderBy('grade_level')->orderBy('section_name')->get();

        // Subjects for filter dropdown
        $subjects = \App\Models\Subject::orderBy('name')->get();
        $academicYears = \App\Models\AcademicYear::orderByDesc('school_year')->get();
        $semesters = ['1st Semester', '2nd Semester', 'Summer'];

        return view('grades.manage', [
            'sections' => $sections,
            'gradeLevels' => $gradeLevels,
            'subjects' => $subjects,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'role' => $role,
            'teacher' => $teacher,
            'allowedSectionIds' => $allowedSectionIds,
            'allowedGradeLevels' => $allowedGradeLevels,
            'gradeFilter' => $gradeFilter,
            'sectionFilter' => $sectionFilter,
            'subjectFilter' => $subjectFilter,
            'academicYearFilter' => $academicYearFilter,
            'semesterFilter' => $semesterFilter,
        ]);
    }

    public function bulk(Request $request)
    {
        $items = $request->input('items', []);
        if (!is_array($items)) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }
        $actor = optional(auth()->user())->getAuthIdentifier();
        $account = auth()->user();
        $saved = 0;
        $errors = [];
        foreach ($items as $i) {
            $enrollmentId = $i['enrollment_id'] ?? null;
            $type = $i['type'] ?? null;
            $score = $i['score'] ?? null;
            if (!$enrollmentId || !$type || $score === null) {
                $errors[] = ['enrollment_id' => $enrollmentId, 'type' => $type, 'message' => 'Missing required fields'];
                continue;
            }
            if (filter_var($score, FILTER_VALIDATE_INT) === false) {
                $errors[] = ['enrollment_id' => $enrollmentId, 'type' => $type, 'message' => 'Score must be an integer'];
                continue;
            }
            $scoreInt = (int) $score;
            if ($scoreInt < 0 || $scoreInt > 100) {
                $errors[] = ['enrollment_id' => $enrollmentId, 'type' => $type, 'message' => 'Score out of range'];
                continue;
            }
            if (!$this->canModifyEnrollment($account, $enrollmentId)) {
                $errors[] = ['enrollment_id' => $enrollmentId, 'type' => $type, 'message' => 'Permission denied'];
                continue;
            }
            $existing = Grade::where('enrollment_ID', $enrollmentId)->where('type', $type)->orderByDesc('date_recorded')->first();
            if ($existing) {
                $old = $existing->score;
                $existing->update(['score' => $scoreInt, 'date_recorded' => now()]);
                try {
                    EnrollmentAudit::create([
                        'enrollment_ID' => $enrollmentId,
                        'processed_by_account_ID' => $actor,
                        'action' => 'grade_bulk_updated',
                        'changes' => json_encode(['type' => $type, 'old' => $old, 'new' => $scoreInt]),
                    ]);
                } catch (\Throwable $e) {}
                $saved++;
            } else {
                $g = Grade::create(['enrollment_id' => $enrollmentId, 'type' => $type, 'score' => $scoreInt, 'date_recorded' => now(), 'weight' => 1]);
                try {
                    EnrollmentAudit::create([
                        'enrollment_ID' => $enrollmentId,
                        'processed_by_account_ID' => $actor,
                        'action' => 'grade_bulk_created',
                        'changes' => json_encode(['type' => $type, 'score' => $scoreInt]),
                    ]);
                } catch (\Throwable $e) {}
                $saved++;
            }
        }
        if (!empty($errors)) {
            return response()->json(['status' => 'partial', 'saved' => $saved, 'errors' => $errors], 422);
        }
        return response()->json(['status' => 'ok', 'saved' => $saved]);
    }
}
