<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentStatusRequest;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentAudit;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Section;
use App\Models\YearLevelAssignment;

class EnrollmentController extends Controller
{
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
    protected function makeCourseCode(Subject $subject, AcademicYear $year): string
    {
        $name = strtoupper(preg_replace('/[^A-Z0-9]+/', '-', $subject->name));
        $name = preg_replace('/-+/', '-', trim($name, '-'));
        $gl = $subject->grade_level ? (string) (int) $subject->grade_level : '';
        $sy = $year->school_year;
        $parts = explode('-', $sy);
        $start = $parts[0] ?? '';
        $sem = $year->semester ?? '';
        $semShort = str_starts_with($sem, '1') ? 'S1' : (str_starts_with($sem, '2') ? 'S2' : 'SM');
        $code = implode('-', array_filter([$name, $gl, $start.$semShort]));
        return $code !== '' ? $code : ('SUBJ-'.$subject->getKey().'-'.$year->getKey());
    }
    public function index()
    {
        $enrollments = Enrollment::with(['student.account', 'course.subject', 'course.teacher'])->paginate(20);

        return view('enrollment.index', compact('enrollments'));
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $data = $request->validated();

        $exists = Enrollment::where('student_ID', $data['student_id'])
            ->where('course_ID', $data['course_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['enrollment' => 'Student already enrolled in this course.']);
        }

        $course = Course::findOrFail($data['course_id']);
        // Prevent duplicate subject enrollment (even across different courses of the same subject)
        $subjectDuplicate = Enrollment::where('enrollment.student_ID', $data['student_id'])
            ->join('courses', 'enrollment.course_ID', '=', 'courses.course_ID')
            ->where('courses.subject_ID', $course->subject_id)
            ->exists();
        if ($subjectDuplicate) {
            return back()->withErrors(['enrollment' => 'Student already enrolled in this subject.']);
        }
        $current = $course->enrollments()->count();
        if ($current >= $course->max_capacity) {
            return back()->withErrors(['capacity' => 'Course capacity reached.']);
        }

        $enrollment = Enrollment::create($data + [
            'status' => $data['status'] ?? 'Enrolled',
            'enrollment_date' => $data['enrollment_date'] ?? now(),
            'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'processed_at' => now(),
        ]);

        EnrollmentAudit::create([
            'enrollment_ID' => $enrollment->getKey(),
            'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'action' => 'created',
            'changes' => json_encode($data),
        ]);

        return redirect()->route('enrollment.index')->with('success', 'Enrolled successfully');
    }

    public function update(UpdateEnrollmentStatusRequest $request, Enrollment $enrollment)
    {
        $data = $request->validated();
        $before = $enrollment->getOriginal();

        $enrollment->update([
            'status' => $data['status'],
            'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'processed_at' => now(),
        ]);

        $changed = array_intersect_key($enrollment->getChanges(), $before);

        EnrollmentAudit::create([
            'enrollment_ID' => $enrollment->getKey(),
            'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'action' => 'updated',
            'changes' => json_encode($changed),
        ]);

        return back()->with('success', 'Enrollment status updated');
    }

    public function destroy(Enrollment $enrollment)
    {
        EnrollmentAudit::create([
            'enrollment_ID' => $enrollment->getKey(),
            'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
            'action' => 'deleted',
            'changes' => null,
        ]);

        $enrollment->delete();

        return back()->with('success', 'Enrollment removed');
    }

    public function drop(Request $request, Enrollment $enrollment)
    {
        $actor = Auth::user();
        if (! $this->canModifyEnrollment($actor, $enrollment->getKey())) {
            EnrollmentAudit::create([
                'enrollment_ID' => $enrollment->getKey(),
                'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
                'action' => 'drop_failed',
                'changes' => json_encode(['reason' => 'permission_denied']),
            ]);
            return back()->withErrors(['permission' => 'You are not allowed to drop this enrollment.']);
        }
        if (strtolower($enrollment->status ?? '') !== 'enrolled') {
            EnrollmentAudit::create([
                'enrollment_ID' => $enrollment->getKey(),
                'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
                'action' => 'drop_skipped',
                'changes' => json_encode(['current_status' => $enrollment->status]),
            ]);
            return back()->withErrors(['status' => 'Enrollment is not active (current status: '.$enrollment->status.').']);
        }
        $beforeStatus = $enrollment->status;
        $enrollment->update([
            'status' => 'Dropped',
            'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
            'processed_at' => now(),
        ]);
        EnrollmentAudit::create([
            'enrollment_ID' => $enrollment->getKey(),
            'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
            'action' => 'dropped',
            'changes' => json_encode(['status' => ['old' => $beforeStatus, 'new' => 'Dropped']]),
        ]);
        return back()->with('success', 'Student dropped from subject');
    }

    public function unenroll(Request $request, Enrollment $enrollment)
    {
        $actor = Auth::user();
        if (strtolower(optional($actor)->role ?? '') !== 'admin') {
            return back()->withErrors(['permission' => 'Only administrators may un-enroll a student.']);
        }
        $beforeStatus = $enrollment->status;
        $enrollment->update([
            'status' => 'Unenrolled',
            'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
            'processed_at' => now(),
        ]);
        EnrollmentAudit::create([
            'enrollment_ID' => $enrollment->getKey(),
            'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
            'action' => 'unenrolled',
            'changes' => json_encode(['status' => ['old' => $beforeStatus, 'new' => 'Unenrolled']]),
        ]);
        return back()->with('success', 'Student un-enrolled from subject');
    }

    /**
     * Automatically enroll a student into all courses for their grade level.
     */
    public function autoEnrollByGrade(Student $student)
    {
        $student->load(['section', 'enrollments.course.subject']);
        $gradeLevel = optional($student->section)->grade_level;
        if (! $gradeLevel) {
            return back()->withErrors(['grade' => 'Student has no grade level (section) assigned.']);
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            return back()->withErrors(['year' => 'No active academic year configured.']);
        }

        $alreadySubjectIds = $student->enrollments->map(function ($enr) {
            return optional($enr->course)->subject_id;
        })->filter()->unique()->all();

        $subjects = \App\Models\Subject::where('grade_level', $gradeLevel)
            ->orderBy('name')
            ->get();

        // Resolve a teacher to assign for placeholder courses
        $teacherId = optional($student->section)->teacher_id;
        if (! $teacherId && $student->section) {
            $firstSectionTeacher = $student->section->teachers()->first();
            if ($firstSectionTeacher) {
                $teacherId = $firstSectionTeacher->teacher_id;
            }
        }
        if (! $teacherId) {
            $anyTeacher = Teacher::first();
            if ($anyTeacher) {
                $teacherId = $anyTeacher->teacher_id;
            }
        }
        if (! $teacherId) {
            return back()->withErrors(['teacher' => 'No teacher available to create courses. Please assign an adviser to the section or create a teacher record.']);
        }

        $created = 0;
        $skippedCapacity = [];
        $skippedDuplicate = [];
        $createdCourses = [];
        foreach ($subjects as $subject) {
            // Skip if subject already assigned via any course
            if (in_array($subject->getKey(), $alreadySubjectIds, true)) {
                $skippedDuplicate[] = $subject->name;

                continue;
            }

            // Find any existing course for this subject in active year, or create a placeholder
            $course = Course::where('subject_id', $subject->getKey())
                ->where('academic_year_id', $activeYear->getKey())
                ->withCount('enrollments')
                ->first();
            if (! $course) {
                $course = Course::create([
                    'subject_id' => $subject->getKey(),
                    'teacher_id' => $teacherId,
                    'academic_year_id' => $activeYear->getKey(),
                    'course_code' => $this->makeCourseCode($subject, $activeYear),
                    'schedule' => 'TBD',
                    'room_number' => 'TBD',
                    'max_capacity' => 40,
                ]);
                $createdCourses[] = $subject->name;
            }

            // Check capacity
            $current = method_exists($course, 'enrollments_count') ? $course->enrollments_count : $course->enrollments()->count();
            if ($course->max_capacity && $current >= $course->max_capacity) {
                $skippedCapacity[] = $subject->name;

                continue;
            }

            // Prevent duplicate enrollment to same course
            $exists = Enrollment::where('student_ID', $student->student_id)
                ->where('course_ID', $course->getKey())
                ->exists();
            if ($exists) {
                $skippedDuplicate[] = $subject->name;

                continue;
            }

            $enrollment = Enrollment::create([
                'student_id' => $student->student_id,
                'course_id' => $course->getKey(),
                'status' => 'Enrolled',
                'enrollment_date' => now(),
                'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
                'processed_at' => now(),
            ]);
            EnrollmentAudit::create([
                'enrollment_ID' => $enrollment->getKey(),
                'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
                'action' => 'created',
                'changes' => json_encode([
                    'student_id' => $student->student_id,
                    'course_id' => $course->getKey(),
                    'status' => 'Enrolled',
                ]),
            ]);
            $created++;
        }

        $msg = "Auto-enrolled {$created} course(s) for Grade {$gradeLevel}.";
        if (count($skippedDuplicate)) {
            $msg .= ' Skipped duplicates: '.implode(', ', array_slice($skippedDuplicate, 0, 5)).(count($skippedDuplicate) > 5 ? '...' : '');
        }
        if (count($skippedCapacity)) {
            $msg .= ' Capacity full: '.implode(', ', array_slice($skippedCapacity, 0, 5)).(count($skippedCapacity) > 5 ? '...' : '');
        }
        if (count($createdCourses)) {
            $msg .= ' Created placeholder courses: '.implode(', ', array_slice($createdCourses, 0, 5)).(count($createdCourses) > 5 ? '...' : '');
        }

        return redirect()->route('students.show', $student)->with('success', $msg);
    }

    /**
     * Manual subject assignment page for administrators.
     */
    public function manualSubjects(Request $request, Student $student)
    {
        $selectedGrade = $request->input('grade');
        $gradeLevels = Subject::select('grade_level')->distinct()->orderBy('grade_level')->pluck('grade_level')->toArray();

        $student->load(['enrollments.course.subject', 'section']);
        $existingSubjectIds = $student->enrollments->map(function ($enr) {
            return optional($enr->course)->subject_id;
        })->filter()->unique()->all();

        $subjects = collect();
        if ($selectedGrade) {
            $subjects = Subject::where('grade_level', $selectedGrade)->orderBy('name')->get();
        }

        $activeYear = AcademicYear::where('is_active', true)->first();
        $coursesBySubject = [];
        if ($activeYear && $subjects->count()) {
            $courses = Course::whereIn('subject_id', $subjects->pluck('subject_id'))
                ->where('academic_year_id', $activeYear->getKey())
                ->with(['subject', 'teacher'])
                ->get()
                ->groupBy('subject_id');
            foreach ($courses as $sid => $list) {
                $coursesBySubject[$sid] = $list;
            }
        }

        // Teachers list for assignment
        $teachers = \App\Models\Teacher::orderBy('last_name')->orderBy('first_name')->get();

        return view('students.subjects', compact(
            'student',
            'gradeLevels',
            'selectedGrade',
            'subjects',
            'existingSubjectIds',
            'coursesBySubject',
            'teachers'
        ));
    }

    /**
     * Assign subjects (via courses) to a student manually.
     */
    public function assignSubjects(Request $request, Student $student)
    {
        $data = $request->validate([
            'grade' => ['required', 'integer'],
            'subject_ids' => ['array'],
            'subject_ids.*' => ['integer'],
            'teacher_ids' => ['array'],
            'teacher_ids.*' => ['integer', 'exists:teachers,teacher_ID'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            return back()->withErrors(['year' => 'No active academic year configured.']);
        }

        $student->load('enrollments.course');
        $existingSubjectIds = $student->enrollments->map(function ($enr) {
            return optional($enr->course)->subject_id;
        })->filter()->unique()->all();

        $subjects = Subject::where('grade_level', $data['grade'])
            ->whereIn('subject_id', $data['subject_ids'] ?? [])
            ->orderBy('name')
            ->get();

        // Resolve teacher for placeholder courses
        $teacherId = optional($student->section)->teacher_id;
        if (! $teacherId && $student->section) {
            $firstSectionTeacher = $student->section->teachers()->first();
            if ($firstSectionTeacher) {
                $teacherId = $firstSectionTeacher->teacher_id;
            }
        }
        if (! $teacherId) {
            $anyTeacher = Teacher::first();
            if ($anyTeacher) {
                $teacherId = $anyTeacher->teacher_id;
            }
        }
        if (! $teacherId) {
            return back()->withErrors(['teacher' => 'No teacher available to create courses. Please assign an adviser to the section or create a teacher record.']);
        }

        $created = 0;
        $duplicates = [];
        $createdCourses = [];
        $selectedTeacherIds = $data['teacher_ids'] ?? [];

        foreach ($subjects as $subject) {
            if (in_array($subject->getKey(), $existingSubjectIds, true)) {
                $duplicates[] = $subject->name;

                continue;
            }
            // pick any course for this subject in active year
            $course = Course::where('subject_id', $subject->getKey())
                ->where('academic_year_id', $activeYear->getKey())
                ->first();
            if (! $course) {
                $course = Course::create([
                    'subject_id' => $subject->getKey(),
                    'teacher_id' => $selectedTeacherIds[$subject->getKey()] ?? $teacherId,
                    'academic_year_id' => $activeYear->getKey(),
                    'course_code' => $this->makeCourseCode($subject, $activeYear),
                    'schedule' => 'TBD',
                    'room_number' => 'TBD',
                    'max_capacity' => 40,
                ]);
                $createdCourses[] = $subject->name;
            } else {
                // Update course teacher if admin provided a different teacher
                if (! empty($selectedTeacherIds[$subject->getKey()])) {
                    $course->update(['teacher_id' => $selectedTeacherIds[$subject->getKey()]]);
                }
            }
            // prevent duplicate enrollment to same course
            $exists = Enrollment::where('student_ID', $student->student_id)
                ->where('course_ID', $course->getKey())
                ->exists();
            if ($exists) {
                $duplicates[] = $subject->name;

                continue;
            }
            $enrollment = Enrollment::create([
                'student_id' => $student->student_id,
                'course_id' => $course->getKey(),
                'status' => 'Enrolled',
                'enrollment_date' => now(),
                'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
                'processed_at' => now(),
            ]);
            EnrollmentAudit::create([
                'enrollment_ID' => $enrollment->getKey(),
                'processed_by_account_ID' => (Auth::user() ? Auth::user()->getAuthIdentifier() : null),
                'action' => 'created',
                'changes' => json_encode([
                    'student_id' => $student->student_id,
                    'course_id' => $course->getKey(),
                    'status' => 'Enrolled',
                ]),
            ]);
            $created++;
        }

        $msg = "Assigned {$created} subject(s).";
        if (count($duplicates)) {
            $msg .= ' Duplicates skipped: '.implode(', ', array_slice($duplicates, 0, 5)).(count($duplicates) > 5 ? '...' : '');
        }
        if (count($createdCourses)) {
            $msg .= ' Created courses: '.implode(', ', array_slice($createdCourses, 0, 5)).(count($createdCourses) > 5 ? '...' : '');
        }

        return redirect()->route('students.subjects', [$student, 'grade' => $data['grade']])->with('success', $msg);
    }

    /**
     * Assign a teacher for a single subject (course) in the active year.
     */
    public function assignTeacher(Request $request, Student $student)
    {
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,subject_id'],
            'teacher_id' => ['required', 'integer', 'exists:teachers,teacher_ID'],
        ]);
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            return back()->withErrors(['year' => 'No active academic year configured.']);
        }
        $course = Course::where('subject_id', $data['subject_id'])
            ->where('academic_year_id', $activeYear->getKey())
            ->first();
        if (! $course) {
            $course = Course::create([
                'subject_id' => $data['subject_id'],
                'teacher_id' => $data['teacher_id'],
                'academic_year_id' => $activeYear->getKey(),
                'course_code' => $this->makeCourseCode(Subject::find($data['subject_id']), $activeYear),
                'schedule' => 'TBD',
                'room_number' => 'TBD',
                'max_capacity' => 40,
            ]);
        } else {
            $course->update(['teacher_id' => $data['teacher_id']]);
        }

        return back()->with('success', 'Teacher assigned to subject.');
    }

    public function dropAllForStudent(Request $request, Student $student)
    {
        $actor = Auth::user();
        $role = strtolower(optional($actor)->role ?? '');
        if (! in_array($role, ['admin', 'teacher'])) {
            return back()->withErrors(['permission' => 'You are not allowed to drop enrollments for this student.']);
        }
        $student->load(['enrollments.student.section']);
        $count = 0;
        $skippedPermission = 0;
        $skippedStatus = 0;
        $total = $student->enrollments->count();
        foreach ($student->enrollments as $enr) {
            $allowed = ($role === 'admin') || $this->canModifyEnrollment($actor, $enr->getKey());
            if (! $allowed) {
                $skippedPermission++;
                EnrollmentAudit::create([
                    'enrollment_ID' => $enr->getKey(),
                    'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
                    'action' => 'drop_failed',
                    'changes' => json_encode(['reason' => 'permission_denied']),
                ]);
                continue;
            }
            if (strtolower($enr->status ?? '') !== 'enrolled') {
                $skippedStatus++;
                EnrollmentAudit::create([
                    'enrollment_ID' => $enr->getKey(),
                    'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
                    'action' => 'drop_skipped',
                    'changes' => json_encode(['current_status' => $enr->status]),
                ]);
                continue;
            }
            $beforeStatus = $enr->status;
            $enr->update([
                'status' => 'Dropped',
                'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
                'processed_at' => now(),
            ]);
            EnrollmentAudit::create([
                'enrollment_ID' => $enr->getKey(),
                'processed_by_account_ID' => $actor ? $actor->getAuthIdentifier() : null,
                'action' => 'dropped',
                'changes' => json_encode(['status' => ['old' => $beforeStatus, 'new' => 'Dropped']]),
            ]);
            $count++;
        }
        if ($count === 0) {
            if ($total === 0) {
                return back()->withErrors(['drop' => 'Student has no enrollments.']);
            }
            if ($skippedStatus === $total) {
                return back()->withErrors(['drop' => 'No active enrollments to drop.']);
            }
            return back()->withErrors(['permission' => 'No enrollments could be dropped due to permission restrictions.']);
        }
        $msg = "Dropped {$count} enrollment(s) for student.";
        if ($skippedStatus > 0) {
            $msg .= " Skipped {$skippedStatus} non-active enrollment(s).";
        }
        if ($skippedPermission > 0) {
            $msg .= " Skipped {$skippedPermission} enrollment(s) due to permission.";
        }
        return back()->with('success', $msg);
    }
}
