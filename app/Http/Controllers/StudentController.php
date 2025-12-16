<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Account;
use App\Models\AccountAudit;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort');
        $dir = strtolower($request->query('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $query = Student::with(['account', 'section', 'enrollments']);
        $q = $request->query('q');
        $gender = $request->query('gender');
        $status = $request->query('status');
        $sectionId = $request->query('section_id');
        $gradeLevel = $request->query('grade_level');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', '%'.$q.'%')
                    ->orWhere('last_name', 'like', '%'.$q.'%')
                    ->orWhereHas('account', function ($aq) use ($q) {
                        $aq->where('Email', 'like', '%'.$q.'%')
                            ->orWhere('Username', 'like', '%'.$q.'%');
                    });
            });
        }
        if ($gender) {
            $val = ucfirst(strtolower($gender));
            $query->where('gender', $val);
        }
        if ($status) {
            $query->where('status', strtolower($status));
        }
        if ($sectionId) {
            $query->where('section_ID', $sectionId);
        }
        if ($gradeLevel) {
            $query->whereHas('section', function ($sq) use ($gradeLevel) {
                $sq->where('grade_level', $gradeLevel);
            });
        }

        if ($sort === 'student_id') {
            $query->orderBy('student_ID', $dir);
        } elseif ($sort === 'name') {
            $query->orderBy('last_name', $dir)->orderBy('first_name', $dir);
        } elseif ($sort === 'grade_level') {
            $query->leftJoin('sections', 'students.section_ID', '=', 'sections.section_ID')
                ->orderBy('sections.grade_level', $dir)
                ->select('students.*');
        } elseif ($sort === 'section') {
            $query->leftJoin('sections', 'students.section_ID', '=', 'sections.section_ID')
                ->orderBy('sections.section_name', $dir)
                ->select('students.*');
        } elseif ($sort === 'status') {
            $query->orderBy('status', $dir);
        }

        $students = $query->paginate(20)->appends($request->query());

        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $maleStudents = Student::where('gender', 'Male')->count();
        $femaleStudents = Student::where('gender', 'Female')->count();

        $sectionsList = \App\Models\Section::orderBy('section_name')->get();
        $grades = \App\Models\Section::distinct()->orderBy('grade_level')->pluck('grade_level')->unique()->toArray();

        return view('students.index', compact(
            'students',
            'totalStudents',
            'activeStudents',
            'maleStudents',
            'femaleStudents',
            'sectionsList',
            'grades',
            'q',
            'gender',
            'status',
            'sectionId',
            'gradeLevel',
            'sort',
            'dir'
        ));
    }

    public function create()
    {
        // Get all grade levels from sections
        $gradeLevels = \App\Models\Section::distinct()->orderBy('grade_level')->pluck('grade_level')->unique()->toArray();

        // Provide available accounts (accounts that don't already have a student)
        // Call get() first to ensure models are hydrated before plucking
        $availableAccounts = \App\Models\Account::whereDoesntHave('student')
            ->get()
            ->mapWithKeys(function ($account) {
                return [$account->account_id => $account->username.' (ID: '.$account->account_id.')'];
            })
            ->toArray();

        // Sections will be loaded via JavaScript based on selected grade level
        $sections = \App\Models\Section::pluck('section_name', 'section_ID')->toArray();

        return view('students.create', compact('availableAccounts', 'sections', 'gradeLevels'));
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();

        $student = Student::create($data);

        return redirect()
            ->route('guardians.create', $student)
            ->with('success', 'Student created. Please add parent / guardian information.');
    }

    public function show(Student $student)
    {
        $student->load('enrollments.course.subject', 'enrollments.course.teacher', 'grades', 'guardians', 'section');

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        if (Auth::check() && Auth::user()->role === 'teacher') {
            $student->load('section.teachers');
            $teacher = Auth::user()->teacher;
            $section = $student->section;
            if (! $teacher || ! $section) {
                abort(403);
            }
            $owns = ($section->teacher_ID && $section->teacher_ID == $teacher->teacher_ID)
                || $section->teachers->contains('teacher_ID', $teacher->teacher_ID);
            if (! $owns) {
                abort(403);
            }
        }
        // For edit, include all accounts but allow current linked account
        $accounts = \App\Models\Account::get()
            ->mapWithKeys(function ($account) {
                return [$account->account_id => $account->username.' (ID: '.$account->account_id.')'];
            })
            ->toArray();
        $sections = \App\Models\Section::pluck('section_name', 'section_ID')->toArray();

        return view('students.edit', compact('student', 'accounts', 'sections'));
    }

    public function update(StoreStudentRequest $request, Student $student)
    {
        if ($request->has('lrn') && $request->input('lrn') !== $student->lrn) {
            AccountAudit::create([
                'actor_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
                'target_account_ID' => ($student->account_ID ?? null),
                'action' => 'lrn_modification_attempt',
                'changes' => json_encode([
                    'student_ID' => $student->student_ID,
                    'field' => 'lrn',
                    'old' => $student->lrn,
                    'attempted' => $request->input('lrn'),
                ]),
            ]);
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'LRN cannot be modified after initial registration',
                ], 400);
            }
            $resp = back()
                ->withInput()
                ->withErrors(['lrn' => 'LRN cannot be modified after initial registration']);
            $resp->setStatusCode(400);

            return $resp;
        }
        if (Auth::check() && Auth::user()->role === 'teacher') {
            $student->load('section.teachers');
            $teacher = Auth::user()->teacher;
            $section = $student->section;
            if (! $teacher || ! $section) {
                abort(403);
            }
            $owns = ($section->teacher_ID && $section->teacher_ID == $teacher->teacher_ID)
                || $section->teachers->contains('teacher_ID', $teacher->teacher_ID);
            if (! $owns) {
                abort(403);
            }
        }
        $student->update($request->validated());

        return redirect()->route('students.show', $student)->with('success', 'Student updated');
    }

    public function destroy(Student $student)
    {
        $actor = Auth::user();
        if (! $actor) abort(403);
        if (! in_array(strtolower($actor->role ?? ''), ['admin', 'teacher'])) abort(403);
        $student->status = 'archived';
        $student->archived_at = now();
        $student->archive_reason = request()->input('reason');
        $student->save();
        try {
            \App\Models\StudentAudit::create([
                'student_ID' => $student->student_ID,
                'actor_account_ID' => $actor->getAuthIdentifier(),
                'action' => 'archived',
                'reason' => $student->archive_reason,
                'changes' => json_encode(['status' => 'archived']),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('students.index')->with('success', 'Student archived');
    }

    public function approve(Student $student)
    {
        $student->status = 'active';
        $student->save();

        return redirect()
            ->route('students.index')
            ->with('success', 'Student approved and activated.');
    }

    public function archive(Request $request, Student $student)
    {
        $actor = Auth::user();
        if (! $actor) abort(403);
        $role = strtolower($actor->role ?? '');
        if (! in_array($role, ['admin', 'teacher'], true)) abort(403);
        if ($student->status === 'archived') {
            return back()->withErrors(['archive' => 'Student is already archived.']);
        }
        $reason = $request->input('archive_reason');
        if ($reason !== null && !is_string($reason)) {
            return back()->withErrors(['archive_reason' => 'Invalid archive reason.']);
        }
        $student->status = 'archived';
        $student->archived_at = now();
        $student->archive_reason = $reason ?: null;
        $student->save();
        try {
            \App\Models\StudentAudit::create([
                'student_ID' => $student->student_ID,
                'actor_account_ID' => $actor->getAuthIdentifier(),
                'action' => 'archived',
                'reason' => $student->archive_reason,
                'changes' => json_encode(['status' => 'archived']),
            ]);
        } catch (\Throwable $e) {}
        return back()->with('success', 'Student archived successfully');
    }

    public function archiveIndex(Request $request)
    {
        $query = Student::with(['account', 'section'])->where('status', 'archived');
        $q = $request->query('q');
        $gender = $request->query('gender');
        $sectionId = $request->query('section_id');
        $gradeLevel = $request->query('grade_level');
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', '%'.$q.'%')
                    ->orWhere('last_name', 'like', '%'.$q.'%')
                    ->orWhereHas('account', function ($aq) use ($q) {
                        $aq->where('Email', 'like', '%'.$q.'%')
                           ->orWhere('Username', 'like', '%'.$q.'%');
                    });
            });
        }
        if ($gender) {
            $val = ucfirst(strtolower($gender));
            $query->where('gender', $val);
        }
        if ($sectionId) {
            $query->where('section_ID', $sectionId);
        }
        if ($gradeLevel) {
            $query->whereHas('section', function ($sq) use ($gradeLevel) {
                $sq->where('grade_level', $gradeLevel);
            });
        }
        $students = $query->paginate(20)->appends($request->query());
        $sectionsList = \App\Models\Section::orderBy('section_name')->get();
        $grades = \App\Models\Section::distinct()->orderBy('grade_level')->pluck('grade_level')->unique()->toArray();

        return view('students.archive', compact('students', 'sectionsList', 'grades', 'q', 'gender', 'sectionId', 'gradeLevel'));
    }

    public function restore(Student $student)
    {
        $actor = Auth::user();
        if (! $actor || strtolower($actor->role ?? '') !== 'admin') {
            abort(403);
        }
        $student->status = 'active';
        $student->archived_at = null;
        $student->archive_reason = null;
        $student->save();
        try {
            \App\Models\StudentAudit::create([
                'student_ID' => $student->student_ID,
                'actor_account_ID' => $actor->getAuthIdentifier(),
                'action' => 'restored',
                'reason' => null,
                'changes' => json_encode(['status' => 'active']),
            ]);
        } catch (\Throwable $e) {}

        return redirect()->route('students.archive')->with('success', 'Student restored');
    }

    public function permanentDelete(Student $student)
    {
        $actor = Auth::user();
        if (! $actor || strtolower($actor->role ?? '') !== 'admin') {
            abort(403);
        }
        if ($student->status !== 'archived') {
            return back()->withErrors(['delete' => 'Only archived students can be permanently deleted.']);
        }
        try {
            \App\Models\StudentAudit::create([
                'student_ID' => $student->student_ID,
                'actor_account_ID' => $actor->getAuthIdentifier(),
                'action' => 'deleted_permanent',
                'reason' => request()->input('reason'),
                'changes' => null,
            ]);
        } catch (\Throwable $e) {}
        $student->delete();
        return redirect()->route('students.archive')->with('success', 'Student permanently deleted');
    }

    public function reEnroll(Request $request, Student $student)
    {
        $actor = Auth::user();
        if (! $actor || strtolower($actor->role ?? '') !== 'admin') {
            abort(403);
        }
        $student->load(['enrollments.course.subject', 'account']);
        $dropped = $student->enrollments->where('status', 'Dropped');
        if ($dropped->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No dropped enrollments to restore.'], 400);
            }
            return back()->withErrors(['reenroll' => 'No dropped enrollments to restore.']);
        }
        $restored = 0;
        foreach ($dropped as $enr) {
            $before = $enr->status;
            $enr->update([
                'status' => 'Enrolled',
                'processed_by_account_ID' => $actor->getAuthIdentifier(),
                'processed_at' => now(),
            ]);
            try {
                \App\Models\EnrollmentAudit::create([
                    'enrollment_ID' => $enr->getKey(),
                    'processed_by_account_ID' => $actor->getAuthIdentifier(),
                    'action' => 're_enrolled',
                    'changes' => json_encode(['status' => ['old' => $before, 'new' => 'Enrolled']]),
                ]);
            } catch (\Throwable $e) {}
            $restored++;
        }
        $oldStatus = $student->status;
        if ($student->status !== 'active') {
            $student->status = 'active';
            $student->save();
            try {
                \App\Models\StudentAudit::create([
                    'student_ID' => $student->student_ID,
                    'actor_account_ID' => $actor->getAuthIdentifier(),
                    'action' => 'status_changed',
                    'reason' => null,
                    'changes' => json_encode(['status' => ['old' => $oldStatus, 'new' => 'active']]),
                ]);
            } catch (\Throwable $e) {}
        }
        try {
            $email = optional($student->account)->Email;
            if ($email) {
                \Illuminate\Support\Facades\Mail::raw('Your enrollment has been restored for previously dropped subjects.', function ($m) use ($email) {
                    $m->to($email)->subject('Re-enrollment Confirmation');
                });
            }
        } catch (\Throwable $e) {}
        if ($request->expectsJson()) {
            return response()->json(['restored' => $restored], 200);
        }
        return back()->with('success', "Re-enrolled {$restored} subject(s) for student.");
    }

    public function bulkPermanentDelete(Request $request)
    {
        $actor = Auth::user();
        if (! $actor || strtolower($actor->role ?? '') !== 'admin') {
            abort(403);
        }
        $ids = (array) $request->input('student_ids', []);
        $students = Student::whereIn('student_ID', $ids)->where('status', 'archived')->get();
        foreach ($students as $student) {
            try {
                \App\Models\StudentAudit::create([
                    'student_ID' => $student->student_ID,
                    'actor_account_ID' => $actor->getAuthIdentifier(),
                    'action' => 'deleted_permanent',
                    'reason' => $request->input('reason'),
                    'changes' => null,
                ]);
            } catch (\Throwable $e) {}
            $student->delete();
        }
        return back()->with('success', 'Selected archived students permanently deleted');
    }

    /**
     * Show enrollment page organized by grade level
     */
    public function enrollment()
    {
        // Get all grade levels with their students
        $gradeLevels = \App\Models\Section::distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level')
            ->unique()
            ->toArray();

        $enrollmentData = [];
        foreach ($gradeLevels as $gradeLevel) {
            $sections = \App\Models\Section::where('grade_level', $gradeLevel)
                ->with(['students' => function ($query) {
                    $query->with('account');
                }])
                ->get();

            $enrollmentData[$gradeLevel] = [
                'sections' => $sections,
                'totalStudents' => $sections->sum(function ($section) {
                    return $section->students->count();
                }),
            ];
        }

        $totalStudents = Student::count();
        $totalEnrolled = Student::count();

        return view('students.enrollment', compact('enrollmentData', 'gradeLevels', 'totalStudents', 'totalEnrolled'));
    }

    /**
     * Get sections for a specific grade level (AJAX endpoint)
     */
    public function getSectionsByGrade($gradeLevel)
    {
        $sections = \App\Models\Section::where('grade_level', $gradeLevel)
            ->pluck('section_name', 'section_ID')
            ->toArray();

        return response()->json($sections);
    }

    public function manageByGrade()
    {
        $account = \Illuminate\Support\Facades\Auth::user();
        $role = strtolower($account->role ?? '');
        $teacher = null;
        $allowedSectionIds = [];
        $allowedGradeLevels = [];
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

        $gradeLevels = \App\Models\Section::distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level')
            ->unique()
            ->toArray();

        $sectionsByGrade = [];
        foreach ($gradeLevels as $gl) {
            $query = \App\Models\Section::where('grade_level', $gl)
                ->with(['students' => function ($q) {
                    $q->with(['account', 'grades', 'enrollments.course']);
                }]);
            if ($role === 'teacher') {
                $query->where(function ($q) use ($allowedSectionIds, $allowedGradeLevels, $gl) {
                    if (!empty($allowedSectionIds)) {
                        $q->whereIn('section_ID', $allowedSectionIds);
                    } elseif (!empty($allowedGradeLevels)) {
                        $q->where('grade_level', $gl)->whereIn('grade_level', $allowedGradeLevels);
                    } else {
                        $q->whereRaw('1=0');
                    }
                });
            }
            $sectionsByGrade[$gl] = $query->get();
        }

        return view('students.manage', [
            'gradeLevels' => $gradeLevels,
            'sectionsByGrade' => $sectionsByGrade,
            'role' => $role,
            'teacher' => $teacher,
            'allowedSectionIds' => $allowedSectionIds,
            'allowedGradeLevels' => $allowedGradeLevels,
        ]);
    }

    public function stats()
    {
        return response()->json([
            'total' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'male' => Student::where('gender', 'Male')->count(),
            'female' => Student::where('gender', 'Female')->count(),
        ]);
    }

    public function export(Request $request)
    {
        $query = Student::with(['account', 'section']);
        $q = $request->query('q');
        $gender = $request->query('gender');
        $status = $request->query('status');
        $sectionId = $request->query('section_id');
        $gradeLevel = $request->query('grade_level');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('first_name', 'like', '%'.$q.'%')
                    ->orWhere('last_name', 'like', '%'.$q.'%')
                    ->orWhereHas('account', function ($aq) use ($q) {
                        $aq->where('Email', 'like', '%'.$q.'%')
                            ->orWhere('Username', 'like', '%'.$q.'%');
                    });
            });
        }
        if ($gender) {
            $val = ucfirst(strtolower($gender));
            $query->where('gender', $val);
        }
        if ($status) {
            $query->where('status', strtolower($status));
        }
        if ($sectionId) {
            $query->where('section_ID', $sectionId);
        }
        if ($gradeLevel) {
            $query->whereHas('section', function ($sq) use ($gradeLevel) {
                $sq->where('grade_level', $gradeLevel);
            });
        }

        $filename = 'students_export_'.date('Ymd_His').'.csv';
        $lines = [];
        $lines[] = implode(',', ['Student ID', 'First Name', 'Last Name', 'Gender', 'Status', 'Email', 'Username', 'Section', 'Grade']);
        $query->chunk(200, function ($chunk) use (&$lines) {
            foreach ($chunk as $s) {
                $email = optional($s->account)->Email ?? '';
                $username = optional($s->account)->Username ?? optional($s->account)->username ?? '';
                $sectionName = optional($s->section)->section_name ?? '';
                $grade = optional($s->section)->grade_level ?? '';
                $row = [
                    $s->student_ID ?? $s->student_id ?? '',
                    $s->first_name,
                    $s->last_name,
                    $s->gender,
                    $s->status,
                    $email,
                    $username,
                    $sectionName,
                    $grade,
                ];
                $escaped = array_map(function ($v) {
                    $t = str_replace('"', '""', (string) $v);

                    return '"'.$t.'"';
                }, $row);
                $lines[] = implode(',', $escaped);
            }
        });
        $csv = implode("\n", $lines)."\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Return paginated enrolled subjects for a student as JSON.
     */
    public function enrolledSubjects(Student $student, Request $request)
    {
        $perPage = (int) ($request->query('per_page', 10));
        $perPage = max(1, min($perPage, 50));
        $page = (int) ($request->query('page', 1));

        $query = \Illuminate\Support\Facades\DB::table('enrollment as e')
            ->join('courses as c', 'e.course_ID', '=', 'c.course_ID')
            ->leftJoin('subjects as s', 'c.subject_ID', '=', 's.subject_ID')
            ->where('e.student_ID', '=', $student->student_ID)
            ->orderByDesc('e.enrollment_date')
            ->select([
                's.subject_ID as subject_id',
                's.name as subject_name',
                'e.enrollment_date',
                'e.status',
            ]);

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function ($row) {
            return [
                'subject_id' => $row->subject_id ?? null,
                'subject_name' => $row->subject_name ?? null,
                'enrollment_date' => optional(\Carbon\Carbon::parse($row->enrollment_date))->format('Y-m-d'),
                'status' => $row->status ?? null,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    public function gradesReport(Request $request, Student $student)
    {
        $account = \Illuminate\Support\Facades\Auth::user();
        $role = strtolower($account->role ?? '');
        $teacher = null;
        $allowedSectionIds = [];
        $allowedGradeLevels = [];
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
        $section = $student->section;
        $canEdit = ($role === 'admin') || ($role === 'teacher' && ($section && (in_array($section->section_ID, $allowedSectionIds) || in_array($section->grade_level, $allowedGradeLevels))));

        $student->load(['account', 'section', 'enrollments.course.subject', 'enrollments.course.academicYear', 'grades']);
        $academicYears = \App\Models\AcademicYear::orderByDesc('school_year')->get();
        $semesters = ['1st Semester', '2nd Semester', 'Summer'];
        $ayFilter = (int) $request->query('academic_year_id', 0);
        $semFilter = $request->query('semester');

        $enrollments = $student->enrollments;
        if ($ayFilter) {
            $enrollments = $enrollments->filter(function ($enr) use ($ayFilter) {
                return optional($enr->course)->academic_year_ID == $ayFilter;
            });
        }
        if ($semFilter) {
            $enrollments = $enrollments->filter(function ($enr) use ($semFilter) {
                $ay = optional($enr->course)->academicYear;
                return $ay && $ay->semester === $semFilter;
            });
        }
        $enrollments = $enrollments->sortBy(function ($enr) {
            return optional($enr->course->subject)->name ?? '';
        });

        return view('students.grades', [
            'student' => $student,
            'enrollments' => $enrollments,
            'role' => $role,
            'canEdit' => $canEdit,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'ayFilter' => $ayFilter,
            'semFilter' => $semFilter,
        ]);
    }
}
