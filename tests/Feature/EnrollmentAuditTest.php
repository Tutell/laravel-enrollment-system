<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Account;
use App\Models\Course;
use App\Models\EnrollmentAudit;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EnrollmentAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrollment_creation_creates_audit_record()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $subject = Subject::create(['name' => 'Math', 'description' => null]);
        $teacherAccount = Account::create([
            'Email' => 'teacher@example.com',
            'Username' => 'teacher1',
            'Password_Hash' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacher = Teacher::create([
            'account_id' => $teacherAccount->account_id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'department' => 'Math',
            'contact_number' => null,
        ]);
        $section = Section::create([
            'section_name' => 'Grade 7-A',
            'grade_level' => 7,
            'capacity' => 40,
        ]);

        $ay = AcademicYear::create([
            'school_year' => '2025-2026',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->subject_id,
            'teacher_id' => $teacher->teacher_id,
            'academic_year_id' => $ay->academic_year_id,
            'course_code' => 'MATH-7-1',
            'schedule' => 'MWF 8-9',
            'room_number' => '101',
            'max_capacity' => 40,
        ]);

        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2005-01-01',
            'status' => 'pending',
            'lrn' => '123456789012',
        ]);

        $this->actingAs($admin, 'web');

        $response = $this->post(route('enrollment.store'), [
            'student_id' => $student->student_id,
            'course_id' => $course->course_id,
            'enrollment_date' => '2025-06-01',
            'status' => 'Enrolled',
        ]);

        $response->assertRedirect(route('enrollment.index'));
        $this->assertEquals(1, EnrollmentAudit::count());
    }
}
