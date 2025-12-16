<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Account;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GradeModificationPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_modify_any_grade()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        $section = Section::create(['section_name' => 'Grade 7-A', 'grade_level' => 7, 'capacity' => 40]);
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2005-01-01',
            'lrn' => '123456789012',
            'section_ID' => $section->section_ID,
            'status' => 'active',
        ]);

        $teacherAccount = Account::create([
            'Email' => 'teacher@example.com',
            'Username' => 'teacher',
            'Password_Hash' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);
        $teacher = Teacher::create([
            'account_ID' => $teacherAccount->account_ID,
            'first_name' => 'Test',
            'last_name' => 'Teacher',
            'department' => 'Math',
        ]);
        $subject = Subject::create(['name' => 'Math', 'grade_level' => 7]);
        $year = AcademicYear::create(['school_year' => '2025-2026', 'semester' => '1st Semester', 'is_active' => true]);
        $course = Course::create([
            'subject_ID' => $subject->subject_ID,
            'teacher_ID' => $teacher->teacher_ID,
            'academic_year_ID' => $year->academic_year_ID,
            'course_code' => 'MATH-7-1',
            'schedule' => 'MWF 8-9',
            'room_number' => '101',
            'max_capacity' => 40,
        ]);
        $enrollment = Enrollment::create([
            'student_id' => $student->student_ID,
            'course_id' => $course->course_ID,
            'enrollment_date' => now(),
            'status' => 'Enrolled',
        ]);

        $resp = $this->post(route('grades.bulk'), [
            'items' => [
                [
                    'enrollment_id' => $enrollment->enrollment_ID,
                    'type' => 'Q1',
                    'score' => 85,
                ],
            ],
        ]);
        $resp->assertStatus(200);
        $this->assertDatabaseHas('grades', [
            'enrollment_ID' => $enrollment->enrollment_ID,
            'type' => 'Q1',
            'score' => 85,
        ]);
    }

    public function test_teacher_can_only_modify_grades_for_assigned_sections()
    {
        $teacherAccount = Account::create([
            'Email' => 'teacher@example.com',
            'Username' => 'teacher',
            'Password_Hash' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);
        $teacher = Teacher::create([
            'account_ID' => $teacherAccount->account_ID,
            'first_name' => 'Test',
            'last_name' => 'Teacher',
            'department' => 'Math',
        ]);
        $this->actingAs($teacherAccount, 'web');

        $assignedSection = Section::create(['section_name' => 'Grade 7-A', 'grade_level' => 7, 'capacity' => 40, 'teacher_ID' => $teacher->teacher_ID]);
        $unassignedSection = Section::create(['section_name' => 'Grade 7-B', 'grade_level' => 7, 'capacity' => 40]);

        $assignedStudent = Student::create([
            'first_name' => 'Assigned',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2005-01-01',
            'lrn' => '123456789012',
            'section_ID' => $assignedSection->section_ID,
            'status' => 'active',
        ]);
        $unassignedStudent = Student::create([
            'first_name' => 'Unassigned',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2005-01-01',
            'lrn' => '123456789013',
            'section_ID' => $unassignedSection->section_ID,
            'status' => 'active',
        ]);

        $subject = Subject::create(['name' => 'Math', 'grade_level' => 7]);
        $year = AcademicYear::create(['school_year' => '2025-2026', 'semester' => '1st Semester', 'is_active' => true]);
        $course = Course::create([
            'subject_ID' => $subject->subject_ID,
            'teacher_ID' => $teacher->teacher_ID,
            'academic_year_ID' => $year->academic_year_ID,
            'course_code' => 'MATH-7-1',
            'schedule' => 'MWF 8-9',
            'room_number' => '101',
            'max_capacity' => 40,
        ]);

        $assignedEnrollment = Enrollment::create([
            'student_id' => $assignedStudent->student_ID,
            'course_id' => $course->course_ID,
            'enrollment_date' => now(),
            'status' => 'Enrolled',
        ]);
        $unassignedEnrollment = Enrollment::create([
            'student_id' => $unassignedStudent->student_ID,
            'course_id' => $course->course_ID,
            'enrollment_date' => now(),
            'status' => 'Enrolled',
        ]);

        $resp = $this->post(route('grades.bulk'), [
            'items' => [
                [
                    'enrollment_id' => $assignedEnrollment->enrollment_ID,
                    'type' => 'Q1',
                    'score' => 85,
                ],
                [
                    'enrollment_id' => $unassignedEnrollment->enrollment_ID,
                    'type' => 'Q1',
                    'score' => 85,
                ],
            ],
        ]);
        $resp->assertStatus(422);
        $this->assertDatabaseHas('grades', [
            'enrollment_ID' => $assignedEnrollment->enrollment_ID,
            'type' => 'Q1',
            'score' => 85,
        ]);
        $this->assertDatabaseMissing('grades', [
            'enrollment_ID' => $unassignedEnrollment->enrollment_ID,
            'type' => 'Q1',
            'score' => 85,
        ]);
    }
}
