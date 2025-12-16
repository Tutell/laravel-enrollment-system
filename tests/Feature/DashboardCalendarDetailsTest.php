<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Account;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardCalendarDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected function seedEnrollmentForToday(): array
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'web');

        $subject = Subject::create(['name' => 'Mathematics', 'description' => null, 'grade_level' => 10]);
        $section = Section::create(['section_name' => 'Grade 10-A', 'grade_level' => 10, 'capacity' => 40]);
        $teacherAcc = Account::create([
            'Email' => 'teacher@example.com',
            'Username' => 'teacher1',
            'Password_Hash' => Hash::make('password'),
            'role' => 'teacher',
        ]);
        $teacherId = \DB::table('teachers')->insertGetId([
            'account_ID' => $teacherAcc->account_ID,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'department' => 'Math',
            'contact_number' => 'N/A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2005-01-01',
            'status' => 'active',
            'lrn' => '123456789012',
            'section_ID' => $section->section_ID,
        ]);
        $ay = AcademicYear::create(['school_year' => '2025-2026', 'semester' => '1st Semester', 'is_active' => true]);
        $course = Course::create([
            'subject_ID' => $subject->subject_ID,
            'teacher_ID' => $teacherId,
            'academic_year_ID' => $ay->academic_year_ID,
            'course_code' => 'MATH-10-AY',
            'schedule' => 'MWF',
            'room_number' => '101',
            'max_capacity' => 40,
        ]);
        $enr = Enrollment::create([
            'student_id' => $student->student_ID,
            'course_id' => $course->course_ID,
            'status' => 'Enrolled',
            'enrollment_date' => now()->toDateString(),
        ]);
        return compact('admin', 'subject', 'section', 'student', 'ay', 'course', 'enr');
    }

    public function test_days_range_returns_labels_and_values(): void
    {
        $this->seedEnrollmentForToday();
        $start = now()->startOfMonth()->toDateString();
        $end = now()->endOfMonth()->toDateString();
        $resp = $this->get(route('dashboard.trend', ['range' => 'days', 'start' => $start, 'end' => $end]));
        $resp->assertStatus(200);
        $data = $resp->json();
        $this->assertArrayHasKey('labels', $data);
        $this->assertArrayHasKey('values', $data);
        $this->assertSame('days', $data['range']);
        $this->assertContains(now()->toDateString(), $data['labels']);
    }

    public function test_date_details_include_items_and_status_breakdown(): void
    {
        $this->seedEnrollmentForToday();
        $today = now()->toDateString();
        $resp = $this->get(route('dashboard.trend', ['date' => $today]));
        $resp->assertStatus(200);
        $data = $resp->json();
        $this->assertEquals($today, $data['date']);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('byStatus', $data);
        $this->assertArrayHasKey('items', $data);
        $this->assertNotEmpty($data['items']);
        $this->assertEquals('Enrolled', $data['items'][0]['status']);
    }

    public function test_days_range_filters_by_academic_year_and_grades(): void
    {
        $seed = $this->seedEnrollmentForToday();
        $today = now()->toDateString();
        $start = now()->startOfMonth()->toDateString();
        $end = now()->endOfMonth()->toDateString();

        $subject2 = \App\Models\Subject::create(['name' => 'Science', 'description' => null, 'grade_level' => 9]);
        $section2 = \App\Models\Section::create(['section_name' => 'Grade 9-B', 'grade_level' => 9, 'capacity' => 40]);
        $student2 = \App\Models\Student::create([
            'first_name' => 'Another',
            'last_name' => 'Learner',
            'gender' => 'Female',
            'birthdate' => '2006-03-03',
            'status' => 'active',
            'lrn' => '999999999999',
            'section_ID' => $section2->section_ID,
        ]);
        $ay2 = \App\Models\AcademicYear::create(['school_year' => '2024-2025', 'semester' => '1st Semester', 'is_active' => false]);
        $course2 = \App\Models\Course::create([
            'subject_ID' => $subject2->subject_ID,
            'teacher_ID' => \DB::table('teachers')->first()->teacher_ID,
            'academic_year_ID' => $ay2->academic_year_ID,
            'course_code' => 'SCI-9-AY',
            'schedule' => 'TTh',
            'room_number' => '202',
            'max_capacity' => 40,
        ]);
        \App\Models\Enrollment::create([
            'student_id' => $student2->student_ID,
            'course_id' => $course2->course_ID,
            'status' => 'Enrolled',
            'enrollment_date' => $today,
        ]);

        $respAll = $this->get(route('dashboard.trend', ['range' => 'days', 'start' => $start, 'end' => $end]));
        $respAll->assertStatus(200);
        $dataAll = $respAll->json();
        $idx = array_search($today, $dataAll['labels'], true);
        $this->assertIsInt($idx);
        $this->assertGreaterThanOrEqual(2, $dataAll['values'][$idx]);

        $respFiltered = $this->get(route('dashboard.trend', [
            'range' => 'days',
            'start' => $start,
            'end' => $end,
            'academic_year_id' => $seed['ay']->academic_year_ID,
            'grades' => '10',
        ]));
        $respFiltered->assertStatus(200);
        $dataFiltered = $respFiltered->json();
        $idx2 = array_search($today, $dataFiltered['labels'], true);
        $this->assertIsInt($idx2);
        $this->assertSame(1, $dataFiltered['values'][$idx2]);
    }

    public function test_stats_endpoint_filters_counts(): void
    {
        $seed = $this->seedEnrollmentForToday();
        $resp = $this->get(route('dashboard.stats', [
            'academic_year_id' => $seed['ay']->academic_year_ID,
            'grades' => '10',
        ]));
        $resp->assertStatus(200);
        $data = $resp->json();
        $this->assertArrayHasKey('students', $data);
        $this->assertArrayHasKey('teachers', $data);
        $this->assertArrayHasKey('subjects', $data);
        $this->assertArrayHasKey('sections', $data);
        $this->assertArrayHasKey('courses', $data);
        $this->assertArrayHasKey('enrollments', $data);
        $this->assertSame(1, $data['students']);
        $this->assertSame(1, $data['subjects']);
        $this->assertSame(1, $data['sections']);
        $this->assertGreaterThanOrEqual(1, $data['teachers']);
        $this->assertSame(1, $data['courses']);
        $this->assertSame(1, $data['enrollments']);
    }
}
