<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentReEnrollTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(): Account
    {
        return Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    protected function baseEntities(): array
    {
        $subject = Subject::create(['name' => 'Mathematics', 'description' => null, 'grade_level' => 10]);
        $teacherAccount = Account::create([
            'Email' => 'teacher@example.com',
            'Username' => 'teacher1',
            'Password_Hash' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);
        $teacher = Teacher::create([
            'account_id' => $teacherAccount->account_id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'department' => 'Math',
            'contact_number' => null,
        ]);
        $section = Section::create([
            'section_name' => 'Grade 10-A',
            'grade_level' => 10,
            'capacity' => 40,
        ]);
        $ay = AcademicYear::create([
            'school_year' => '2025-2026',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_ID' => $subject->subject_ID,
            'teacher_ID' => $teacher->teacher_ID,
            'academic_year_ID' => $ay->academic_year_ID,
            'course_code' => 'MATH-10-AY',
            'schedule' => 'MWF',
            'room_number' => '101',
            'max_capacity' => 40,
        ]);
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2005-01-01',
            'status' => 'inactive',
            'lrn' => '123456789012',
            'section_ID' => $section->section_ID,
        ]);
        return compact('subject', 'teacherAccount', 'teacher', 'section', 'ay', 'course', 'student');
    }

    public function test_admin_can_reenroll_dropped_enrollments(): void
    {
        $admin = $this->makeAdmin();
        $entities = $this->baseEntities();
        $student = $entities['student'];
        $course = $entities['course'];
        Enrollment::create(['student_id' => $student->student_ID, 'course_id' => $course->course_ID, 'status' => 'Dropped', 'enrollment_date' => now()]);
        $this->actingAs($admin, 'web');

        $resp = $this->post(route('students.re-enroll', $student));
        $resp->assertRedirect();
        $student->refresh();
        $this->assertSame('active', $student->status);
        $this->assertDatabaseHas('enrollment', [
            'student_ID' => $student->student_ID,
            'course_ID' => $course->course_ID,
            'status' => 'Enrolled',
        ]);
    }

    public function test_non_admin_cannot_reenroll(): void
    {
        $entities = $this->baseEntities();
        $student = $entities['student'];
        $course = $entities['course'];
        Enrollment::create(['student_id' => $student->student_ID, 'course_id' => $course->course_ID, 'status' => 'Dropped', 'enrollment_date' => now()]);
        $teacherAcc = $entities['teacherAccount'];
        $this->actingAs($teacherAcc, 'web');
        $resp = $this->post(route('students.re-enroll', $student));
        $resp->assertStatus(403);
    }

    public function test_reenroll_without_dropped_enrollments_returns_error(): void
    {
        $admin = $this->makeAdmin();
        $entities = $this->baseEntities();
        $student = $entities['student'];
        $this->actingAs($admin, 'web');
        $resp = $this->post(route('students.re-enroll', $student));
        $resp->assertRedirect();
        $resp->assertSessionHasErrors(['reenroll']);
    }
}
