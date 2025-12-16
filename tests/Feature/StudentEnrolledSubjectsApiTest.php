<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Account;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

class StudentEnrolledSubjectsApiTest extends TestCase
{
    use RefreshDatabase;

    private function loginAdmin()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');
    }

    public function test_returns_paginated_enrolled_subjects()
    {
        $this->loginAdmin();

        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => now()->subYears(12)->toDateString(),
            'lrn' => '123456789012',
        ]);

        $subject = Subject::create(['name' => 'Mathematics']);
        $teacherAccount = Account::create([
            'Email' => 'teacher1@example.com',
            'Username' => 'teacher1',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'teacher',
            'status' => 'active',
        ]);
        DB::table('teachers')->insert([
            'teacher_ID' => 1,
            'account_ID' => $teacherAccount->account_ID,
            'first_name' => 'T',
            'last_name' => 'One',
            'contact_number' => 'N/A',
            'department' => 'Math',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $ay = AcademicYear::create([
            'school_year' => '2025-2026',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);
        DB::table('courses')->insert([
            'subject_ID' => $subject->subject_id,
            'teacher_ID' => 1,
            'academic_year_ID' => $ay->academic_year_id,
            'course_code' => 'MATH-101',
            'schedule' => 'TBD',
            'room_number' => 'TBD',
            'max_capacity' => 40,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $courseId = DB::getPdo()->lastInsertId();

        Enrollment::create([
            'student_id' => $student->student_ID,
            'course_id' => (int) $courseId,
            'enrollment_date' => now()->toDateString(),
            'status' => 'Enrolled',
        ]);

        $resp = $this->getJson(route('students.enrolled-subjects', $student) . '?per_page=10&page=1');
        $resp->assertOk();
        $json = $resp->json();
        $this->assertArrayHasKey('data', $json);
        $this->assertCount(1, $json['data']);
        $this->assertSame('Mathematics', $json['data'][0]['subject_name']);
        $this->assertNotEmpty($json['data'][0]['status']);
    }
}
