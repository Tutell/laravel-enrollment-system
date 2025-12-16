<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'subjects' => Subject::count(),
            'sections' => Section::count(),
            'courses' => Course::count(),
            'academicYears' => AcademicYear::count(),
            'enrollments' => Enrollment::count(),
            'grades' => Grade::count(),
        ];

        $now = Carbon::now();
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $dt = $now->copy()->subMonths($i);
            $months[] = [
                'key' => $dt->format('Y-m'),
                'label' => $dt->format('M'),
                'year' => $dt->year,
            ];
        }

        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', enrollment_date)"
            : ($driver === 'pgsql'
                ? "to_char(enrollment_date, 'YYYY-MM')"
                : "DATE_FORMAT(enrollment_date, '%Y-%m')");

        $rows = DB::table('enrollment')
            ->selectRaw("$monthExpr as ym, COUNT(*) as cnt")
            ->where('status', 'Enrolled')
            ->whereBetween('enrollment_date', [$now->copy()->subMonths(11)->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $trendValues = array_map(function ($m) use ($rows) {
            return (int) ($rows[$m['key']]->cnt ?? 0);
        }, $months);

        $trendLabels = array_map(fn ($m) => $m['label'], $months);

        return view('admin.dashboard', compact('stats', 'trendLabels', 'trendValues'));
    }

    public function trend(Request $request)
    {
        $range = $request->query('range', 'months');
        $date = $request->query('date'); // detail for a specific date
        $start = $request->query('start');
        $end = $request->query('end');
        $academicYearId = $request->query('academic_year_id');
        $gradesParam = trim((string) $request->query('grades', ''));
        $grades = array_values(array_filter(array_map(function ($v) {
            $v = trim($v);
            return $v === '' ? null : (is_numeric($v) ? (int) $v : $v);
        }, explode(',', $gradesParam)), function ($v) {
            return $v !== null;
        }));
        $range = in_array($range, ['days', 'weeks', 'months']) ? $range : 'months';

        // Detail payload when a specific date is requested
        if ($date) {
            $driver = DB::connection()->getDriverName();
            $dayExpr = $driver === 'sqlite'
                ? "date(enrollment_date)"
                : ($driver === 'pgsql'
                    ? "to_char(enrollment_date, 'YYYY-MM-DD')"
                    : "DATE(enrollment_date)");

            $totalQuery = DB::table('enrollment')
                ->where('enrollment.status', 'Enrolled')
                ->whereRaw("$dayExpr = ?", [$date]);
            if ($academicYearId) {
                $totalQuery->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                    ->where('courses.academic_year_ID', '=', $academicYearId);
            }
            if (count($grades)) {
                $totalQuery->join('students', 'students.student_ID', '=', 'enrollment.student_ID')
                    ->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                    ->whereIn('sections.grade_level', $grades);
            }
            $total = $totalQuery->count();

            $byGradeQuery = DB::table('enrollment')
                ->selectRaw("sections.grade_level as grade, COUNT(*) as cnt")
                ->join('students', 'students.student_ID', '=', 'enrollment.student_ID')
                ->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                ->where('enrollment.status', 'Enrolled')
                ->whereRaw("$dayExpr = ?", [$date]);
            if ($academicYearId) {
                $byGradeQuery->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                    ->where('courses.academic_year_ID', '=', $academicYearId);
            }
            if (count($grades)) {
                $byGradeQuery->whereIn('sections.grade_level', $grades);
            }
            $byGrade = $byGradeQuery
                ->groupBy('sections.grade_level')
                ->orderBy('sections.grade_level')
                ->get();

            // Full list with optional filters
            $gradeFilter = $request->query('grade'); // numeric grade level
            $sectionFilter = $request->query('section'); // section_ID
            $concat = $driver === 'sqlite'
                ? "COALESCE(students.first_name,'') || ' ' || COALESCE(students.last_name,'')"
                : "CONCAT(COALESCE(students.first_name,''),' ',COALESCE(students.last_name,''))";
            $itemsQuery = DB::table('enrollment')
                ->join('students', 'students.student_ID', '=', 'enrollment.student_ID')
                ->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                ->join('subjects', 'subjects.subject_ID', '=', 'courses.subject_ID')
                ->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                ->selectRaw("
                    enrollment.enrollment_ID as id,
                    students.student_ID as student_id,
                    $concat as student_name,
                    sections.section_name as section,
                    sections.grade_level as grade_level,
                    subjects.name as subject,
                    enrollment.status as status,
                    enrollment.enrollment_date as date
                ")
                ->whereRaw("$dayExpr = ?", [$date]);
            if ($academicYearId) {
                $itemsQuery->where('courses.academic_year_ID', '=', $academicYearId);
            }
            if (count($grades)) {
                $itemsQuery->whereIn('sections.grade_level', $grades);
            }
            if ($gradeFilter) {
                $itemsQuery->where('sections.grade_level', '=', $gradeFilter);
            }
            if ($sectionFilter) {
                $itemsQuery->where('sections.section_ID', '=', $sectionFilter);
            }
            $items = $itemsQuery->limit(200)->get();

            // Status breakdown
            $statusQuery = DB::table('enrollment')
                ->selectRaw("enrollment.status as status, COUNT(*) as cnt")
                ->whereRaw("$dayExpr = ?", [$date]);
            if ($academicYearId) {
                $statusQuery->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                    ->where('courses.academic_year_ID', '=', $academicYearId);
            }
            if (count($grades)) {
                $statusQuery->join('students', 'students.student_ID', '=', 'enrollment.student_ID')
                    ->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                    ->whereIn('sections.grade_level', $grades);
            }
            $statusRows = $statusQuery
                ->groupBy('enrollment.status')
                ->get()
                ->pluck('cnt', 'status');

            return response()->json([
                'date' => $date,
                'count' => (int) $total,
                'byGrade' => $byGrade,
                'byStatus' => $statusRows,
                'items' => $items,
            ]);
        }

        if ($range === 'days') {
            $driver = DB::connection()->getDriverName();
            $dayExpr = $driver === 'sqlite'
                ? "date(enrollment_date)"
                : ($driver === 'pgsql'
                    ? "to_char(enrollment_date, 'YYYY-MM-DD')"
                    : "DATE(enrollment_date)");

            // Default to current month if start/end not provided
            $startDate = $start ?: Carbon::now()->startOfMonth()->toDateString();
            $endDate = $end ?: Carbon::now()->endOfMonth()->toDateString();

            $dailyQuery = DB::table('enrollment')
                ->selectRaw("$dayExpr as d, COUNT(*) as cnt")
                ->where('enrollment.status', 'Enrolled')
                ->whereBetween('enrollment.enrollment_date', [$startDate, $endDate]);
            if ($academicYearId) {
                $dailyQuery->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                    ->where('courses.academic_year_ID', '=', $academicYearId);
            }
            if (count($grades)) {
                $dailyQuery->join('students', 'students.student_ID', '=', 'enrollment.student_ID')
                    ->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                    ->whereIn('sections.grade_level', $grades);
            }
            $rows = $dailyQuery
                ->groupBy('d')
                ->orderBy('d')
                ->get()
                ->keyBy('d');

            // Build continuous date axis for label consistency
            $labels = [];
            $values = [];
            $cursor = Carbon::parse($startDate);
            $until = Carbon::parse($endDate);
            while ($cursor->lte($until)) {
                $key = $cursor->toDateString();
                $labels[] = $key;
                $values[] = (int) ($rows[$key]->cnt ?? 0);
                $cursor->addDay();
            }

            return response()->json([
                'labels' => $labels,
                'values' => $values,
                'range' => 'days',
                'start' => $startDate,
                'end' => $endDate,
            ]);
        } elseif ($range === 'weeks') {
            $now = Carbon::now();
            $weeks = [];
            for ($i = 11; $i >= 0; $i--) {
                $start = $now->copy()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
                $weeks[] = [
                    'key' => $start->format('oW'),
                    'label' => 'Wk '.$start->format('W'),
                    'start' => $start->toDateString(),
                    'end' => $start->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
                ];
            }

            $driver = DB::connection()->getDriverName();
            $weekExpr = $driver === 'sqlite'
                ? "strftime('%Y%W', enrollment_date)"
                : ($driver === 'pgsql'
                    ? "to_char(enrollment_date, 'IYYYIW')"
                    : "DATE_FORMAT(enrollment_date, '%x%v')");

            $weeklyQuery = DB::table('enrollment')
                ->selectRaw("$weekExpr as yw, COUNT(*) as cnt")
                ->where('enrollment.status', 'Enrolled')
                ->whereBetween('enrollment.enrollment_date', [$weeks[0]['start'], end($weeks)['end']]);
            if ($academicYearId) {
                $weeklyQuery->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                    ->where('courses.academic_year_ID', '=', $academicYearId);
            }
            if (count($grades)) {
                $weeklyQuery->join('students', 'students.student_ID', '=', 'enrollment.student_ID')
                    ->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                    ->whereIn('sections.grade_level', $grades);
            }
            $rows = $weeklyQuery
                ->groupBy('yw')
                ->orderBy('yw')
                ->get()
                ->keyBy('yw');

            $values = array_map(function ($w) use ($rows) {
                return (int) ($rows[$w['key']]->cnt ?? 0);
            }, $weeks);
            $labels = array_map(fn ($w) => $w['label'], $weeks);
        } else {
            $now = Carbon::now();
            $months = [];
            for ($i = 11; $i >= 0; $i--) {
                $dt = $now->copy()->subMonths($i);
                $months[] = [
                    'key' => $dt->format('Y-m'),
                    'label' => $dt->format('M'),
                    'start' => $dt->copy()->startOfMonth()->toDateString(),
                    'end' => $dt->copy()->endOfMonth()->toDateString(),
                ];
            }

            $driver = DB::connection()->getDriverName();
            $monthExpr = $driver === 'sqlite'
                ? "strftime('%Y-%m', enrollment_date)"
                : ($driver === 'pgsql'
                    ? "to_char(enrollment_date, 'YYYY-MM')"
                    : "DATE_FORMAT(enrollment_date, '%Y-%m')");

            $monthlyQuery = DB::table('enrollment')
                ->selectRaw("$monthExpr as ym, COUNT(*) as cnt")
                ->where('enrollment.status', 'Enrolled')
                ->whereBetween('enrollment.enrollment_date', [$months[0]['start'], end($months)['end']]);
            if ($academicYearId) {
                $monthlyQuery->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                    ->where('courses.academic_year_ID', '=', $academicYearId);
            }
            if (count($grades)) {
                $monthlyQuery->join('students', 'students.student_ID', '=', 'enrollment.student_ID')
                    ->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                    ->whereIn('sections.grade_level', $grades);
            }
            $rows = $monthlyQuery
                ->groupBy('ym')
                ->orderBy('ym')
                ->get()
                ->keyBy('ym');

            $values = array_map(function ($m) use ($rows) {
                return (int) ($rows[$m['key']]->cnt ?? 0);
            }, $months);
            $labels = array_map(fn ($m) => $m['label'], $months);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'range' => $range,
        ]);
    }

    public function stats(Request $request)
    {
        $academicYearId = $request->query('academic_year_id');
        $gradesParam = trim((string) $request->query('grades', ''));
        $grades = array_values(array_filter(array_map(function ($v) {
            $v = trim($v);
            return $v === '' ? null : (is_numeric($v) ? (int) $v : $v);
        }, explode(',', $gradesParam)), function ($v) {
            return $v !== null;
        }));

        if (! $academicYearId && ! count($grades)) {
            return response()->json([
                'students' => Student::count(),
                'teachers' => Teacher::count(),
                'subjects' => Subject::count(),
                'sections' => Section::count(),
                'courses' => Course::count(),
                'enrollments' => Enrollment::count(),
            ]);
        }

        $studentIdsQuery = DB::table('enrollment')
            ->selectRaw('DISTINCT enrollment.student_ID as sid')
            ->join('students', 'students.student_ID', '=', 'enrollment.student_ID');
        if ($academicYearId) {
            $studentIdsQuery->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                ->where('courses.academic_year_ID', '=', $academicYearId);
        }
        if (count($grades)) {
            $studentIdsQuery->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                ->whereIn('sections.grade_level', $grades);
        }
        $studentIds = $studentIdsQuery->pluck('sid');

        $teacherIdsQuery = DB::table('courses')
            ->selectRaw('DISTINCT courses.teacher_ID as tid');
        if ($academicYearId) {
            $teacherIdsQuery->where('courses.academic_year_ID', '=', $academicYearId);
        }
        if (count($grades)) {
            $teacherIdsQuery->join('subjects', 'subjects.subject_ID', '=', 'courses.subject_ID')
                ->whereIn('subjects.grade_level', $grades);
        }
        $teacherIds = $teacherIdsQuery->pluck('tid');

        $subjectIdsQuery = DB::table('courses')
            ->selectRaw('DISTINCT courses.subject_ID as sid');
        if ($academicYearId) {
            $subjectIdsQuery->where('courses.academic_year_ID', '=', $academicYearId);
        }
        if (count($grades)) {
            $subjectIdsQuery->join('subjects', 'subjects.subject_ID', '=', 'courses.subject_ID')
                ->whereIn('subjects.grade_level', $grades);
        }
        $subjectIds = $subjectIdsQuery->pluck('sid');

        $sectionIdsQuery = DB::table('sections')->select('sections.section_ID');
        if (count($grades)) {
            $sectionIdsQuery->whereIn('sections.grade_level', $grades);
        }
        $sectionIds = $sectionIdsQuery->pluck('section_ID');

        $courseIdsQuery = DB::table('courses')->select('courses.course_ID');
        if ($academicYearId) {
            $courseIdsQuery->where('courses.academic_year_ID', '=', $academicYearId);
        }
        if (count($grades)) {
            $courseIdsQuery->join('subjects', 'subjects.subject_ID', '=', 'courses.subject_ID')
                ->whereIn('subjects.grade_level', $grades);
        }
        $courseIds = $courseIdsQuery->pluck('course_ID');

        $enrollmentsQuery = DB::table('enrollment')->select('enrollment.enrollment_ID');
        if ($academicYearId) {
            $enrollmentsQuery->join('courses', 'courses.course_ID', '=', 'enrollment.course_ID')
                ->where('courses.academic_year_ID', '=', $academicYearId);
        }
        if (count($grades)) {
            $enrollmentsQuery->join('students', 'students.student_ID', '=', 'enrollment.student_ID')
                ->leftJoin('sections', 'sections.section_ID', '=', 'students.section_ID')
                ->whereIn('sections.grade_level', $grades);
        }
        $enrollmentIds = $enrollmentsQuery->pluck('enrollment_ID');

        return response()->json([
            'students' => Student::whereIn('student_ID', $studentIds)->count(),
            'teachers' => Teacher::whereIn('teacher_ID', $teacherIds)->count(),
            'subjects' => Subject::whereIn('subject_ID', $subjectIds)->count(),
            'sections' => Section::whereIn('section_ID', $sectionIds)->count(),
            'courses' => Course::whereIn('course_ID', $courseIds)->count(),
            'enrollments' => Enrollment::whereIn('enrollment_ID', $enrollmentIds)->count(),
        ]);
    }
}
