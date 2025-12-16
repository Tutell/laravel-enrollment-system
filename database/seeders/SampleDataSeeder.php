<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Account;
use App\Models\Course;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        $ay = AcademicYear::updateOrCreate(
            ['school_year' => '2025-2026', 'semester' => '1st Semester'],
            ['is_active' => true]
        );

        $sub = Subject::updateOrCreate(['name' => 'Algebra I'], ['description' => 'Basic algebra course']);

        $tAcc = Account::updateOrCreate(
            ['Email' => 'teacher1@example.com'],
            [
                'Username' => 'teacher1',
                'Password_Hash' => bcrypt('teacherpass'),
                'role' => 'teacher',
            ]
        );

        $teacher = Teacher::updateOrCreate(
            ['account_ID' => $tAcc->account_ID],
            ['first_name' => 'John', 'last_name' => 'Doe', 'department' => 'Math']
        );

        $section = Section::updateOrCreate(
            ['section_name' => 'Grade 9-A', 'grade_level' => 9],
            ['teacher_ID' => $teacher->teacher_ID, 'capacity' => 40]
        );

        $course = Course::updateOrCreate(
            ['course_code' => 'MATH101-1'],
            [
                'subject_id' => $sub->subject_id,
                'teacher_id' => $teacher->teacher_id ?? $teacher->teacher_ID ?? null,
                'academic_year_id' => $ay->academic_year_id,
                'schedule' => 'MWF 10:00-11:00',
                'room_number' => 'R101',
                'max_capacity' => 30,
            ]
        );

        $sAcc = Account::updateOrCreate(
            ['Email' => 'student1@example.com'],
            [
                'Username' => 'student1',
                'Password_Hash' => bcrypt('studentpass'),
                'role' => 'student',
            ]
        );

        $student = Student::updateOrCreate(
            ['account_ID' => $sAcc->account_ID],
            [
                'section_ID' => $section->section_ID ?? $section->section_id ?? null,
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'gender' => 'Female',
                'birthdate' => '2008-04-21',
            ]
        );
    }
}
