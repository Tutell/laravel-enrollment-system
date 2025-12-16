<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Seed year levels 7–10
        foreach ([7, 8, 9, 10] as $grade) {
            DB::table('year_levels')->updateOrInsert(
                ['grade_level' => $grade],
                ['student_count' => 0, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Compute student counts per grade from sections/students
        $counts = DB::table('students')
            ->join('sections', 'students.section_ID', '=', 'sections.section_ID')
            ->select('sections.grade_level', DB::raw('COUNT(*) as cnt'))
            ->groupBy('sections.grade_level')
            ->pluck('cnt', 'sections.grade_level');

        foreach ($counts as $grade => $cnt) {
            DB::table('year_levels')->where('grade_level', $grade)->update(['student_count' => (int) $cnt]);
        }

        // Transform courses to teacher assignments based on subject.grade_level
        // Existing courses without subject grade_level will be skipped
        $courses = DB::table('courses')
            ->join('subjects', 'courses.subject_ID', '=', 'subjects.subject_ID')
            ->select('courses.teacher_ID', 'subjects.grade_level')
            ->whereNotNull('subjects.grade_level')
            ->get();

        foreach ($courses as $c) {
            $yl = DB::table('year_levels')->where('grade_level', $c->grade_level)->first();
            if (! $yl) {
                continue;
            }
            // Approve existing assignments derived from courses
            DB::table('year_level_assignments')->updateOrInsert(
                ['year_level_ID' => $yl->year_level_ID, 'teacher_ID' => $c->teacher_ID],
                [
                    'status' => 'approved',
                    'requested_at' => now(),
                    'approved_at' => now(),
                    'notes' => 'Migrated from Courses module',
                    'updated_at' => now(),
                ] + ['created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        // No-op: keep year levels and assignments
    }
};
