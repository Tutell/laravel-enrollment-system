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
use Tests\TestCase;

class EnrollmentDropFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAccount(string $role = 'admin'): Account
    {
        return Account::create([
            'Username' => 'user_'.$role.'_'.uniqid(),
            'Password_Hash' => bcrypt('password'),
            'Email' => $role.'_'.uniqid().'@example.test',
            'role' => $role,
            'status' => 'active',
        ]);
    }

    protected function baseEntities(): array
    {
        $year = AcademicYear::create(['school_year' => '2025-2026', 'semester' => '1st Semester', 'is_active' => true]);
        $subject = Subject::create(['name' => 'Mathematics', 'description' => 'Math', 'grade_level' => 10]);
        $teacherAccount = $this->makeAccount('teacher');
        $teacher = Teacher::create(['account_ID' => $teacherAccount->account_id, 'first_name' => 'T', 'last_name' => 'E', 'department' => 'Science']);
        $section = Section::create(['section_name' => 'A', 'grade_level' => 10, 'capacity' => 40, 'teacher_ID' => $teacher->teacher_ID]);
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2000-01-01',
            'status' => 'active',
            'email' => 'student@example.test',
            'phone' => '0000000000',
            'lrn' => '123456789012',
            'section_ID' => $section->section_ID,
        ]);
        $course = Course::create([
            'subject_ID' => $subject->subject_ID,
            'teacher_ID' => $teacher->teacher_ID,
            'academic_year_ID' => $year->academic_year_ID,
            'course_code' => 'MATH-10-AY',
            'schedule' => 'MWF',
            'room_number' => '101',
            'max_capacity' => 40,
        ]);
        return compact('year', 'subject', 'teacherAccount', 'teacher', 'section', 'student', 'course');
    }

    public function test_admin_drop_all_success(): void
    {
        $admin = $this->makeAccount('admin');
        $entities = $this->baseEntities();
        $student = $entities['student'];
        $course = $entities['course'];
        Enrollment::create(['student_id' => $student->student_ID, 'course_id' => $course->course_ID, 'status' => 'Enrolled', 'enrollment_date' => now()]);
        $this->actingAs($admin);

        $resp = $this->post(route('students.drop-all', $student));
        $resp->assertRedirect();
        $student->load('enrollments');
        $this->assertSame('Dropped', $student->enrollments->first()->status);
    }

    public function test_teacher_with_permission_can_drop_all(): void
    {
        $entities = $this->baseEntities();
        $teacherAccount = $entities['teacherAccount'];
        $teacher = $entities['teacher'];
        $student = $entities['student'];
        $course = $entities['course'];
        Enrollment::create(['student_id' => $student->student_ID, 'course_id' => $course->course_ID, 'status' => 'Enrolled', 'enrollment_date' => now()]);
        $this->actingAs($teacherAccount);

        $resp = $this->post(route('students.drop-all', $student));
        $resp->assertRedirect();
        $student->load('enrollments');
        $this->assertSame('Dropped', $student->enrollments->first()->status);
    }

    public function test_teacher_without_permission_gets_specific_error(): void
    {
        $entities = $this->baseEntities();
        $student = $entities['student'];
        $course = $entities['course'];
        Enrollment::create(['student_id' => $student->student_ID, 'course_id' => $course->course_ID, 'status' => 'Enrolled', 'enrollment_date' => now()]);
        $otherTeacherAcc = $this->makeAccount('teacher');
        $this->actingAs($otherTeacherAcc);

        $resp = $this->post(route('students.drop-all', $student));
        $resp->assertRedirect();
        $resp->assertSessionHasErrors(['permission' => 'No enrollments could be dropped due to permission restrictions.']);
        $student->load('enrollments');
        $this->assertSame('Enrolled', $student->enrollments->first()->status);
    }

    public function test_no_active_enrollments_gives_clear_message(): void
    {
        $admin = $this->makeAccount('admin');
        $entities = $this->baseEntities();
        $student = $entities['student'];
        $course = $entities['course'];
        Enrollment::create(['student_id' => $student->student_ID, 'course_id' => $course->course_ID, 'status' => 'Dropped', 'enrollment_date' => now()]);
        $this->actingAs($admin);

        $resp = $this->post(route('students.drop-all', $student));
        $resp->assertRedirect();
        $resp->assertSessionHasErrors(['drop' => 'No active enrollments to drop.']);
    }

    public function test_student_has_no_enrollments_message(): void
    {
        $admin = $this->makeAccount('admin');
        $entities = $this->baseEntities();
        $student = $entities['student'];
        $this->actingAs($admin);

        $resp = $this->post(route('students.drop-all', $student));
        $resp->assertRedirect();
        $resp->assertSessionHasErrors(['drop' => 'Student has no enrollments.']);
    }

    public function test_single_drop_skips_non_enrolled(): void
    {
        $admin = $this->makeAccount('admin');
        $entities = $this->baseEntities();
        $student = $entities['student'];
        $course = $entities['course'];
        $enr = Enrollment::create(['student_id' => $student->student_ID, 'course_id' => $course->course_ID, 'status' => 'Dropped', 'enrollment_date' => now()]);
        $this->actingAs($admin);

        $resp = $this->post(route('enrollment.drop', $enr));
        $resp->assertRedirect();
        $resp->assertSessionHasErrors(['status' => 'Enrollment is not active (current status: Dropped).']);
    }
}
