<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\YearLevel;
use App\Models\YearLevelAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeachersIndexEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAccount(string $role = 'admin', string $status = 'active'): Account
    {
        return Account::create([
            'Username' => 'user_'.$role.'_'.uniqid(),
            'Password_Hash' => bcrypt('password'),
            'Email' => $role.'_'.uniqid().'@example.test',
            'role' => $role,
            'status' => $status,
        ]);
    }

    protected function seedTeacherWithData(string $status = 'active'): array
    {
        $admin = $this->makeAccount('admin');
        $teacherAcc = $this->makeAccount('teacher', $status);
        $teacher = Teacher::create(['account_ID' => $teacherAcc->account_id, 'first_name' => 'Alice', 'last_name' => 'Teacher', 'department' => 'Science']);

        $yl = YearLevel::firstOrCreate(['grade_level' => 10]);
        YearLevelAssignment::create(['teacher_ID' => $teacher->teacher_ID, 'year_level_ID' => $yl->year_level_ID, 'status' => 'approved']);

        $subject = Subject::create(['name' => 'Mathematics', 'description' => 'Math', 'grade_level' => 10]);
        $ay = AcademicYear::create(['school_year' => '2025-2026', 'semester' => '1st Semester', 'is_active' => true]);
        $course = Course::create([
            'subject_ID' => $subject->subject_ID,
            'teacher_ID' => $teacher->teacher_ID,
            'academic_year_ID' => $ay->academic_year_ID,
            'course_code' => 'MATH-10-AY',
            'schedule' => 'MWF',
            'room_number' => '101',
            'max_capacity' => 40,
        ]);

        return compact('admin', 'teacherAcc', 'teacher', 'yl', 'subject', 'ay', 'course');
    }

    public function test_teachers_index_shows_assigned_grade_and_subjects(): void
    {
        $entities = $this->seedTeacherWithData('active');
        $this->actingAs($entities['admin']);

        $resp = $this->get(route('teachers.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Assigned Grades', false);
        $resp->assertSee('Subjects', false);
        $resp->assertSee('G10', false);
        $resp->assertSee('Mathematics', false);
    }

    public function test_filters_by_status_grade_subject(): void
    {
        $entities = $this->seedTeacherWithData('on_leave');
        $this->actingAs($entities['admin']);

        $resp = $this->get(route('teachers.index', ['status' => 'on_leave', 'grade_level' => 10, 'subject_id' => $entities['subject']->subject_ID]));
        $resp->assertStatus(200);
        $resp->assertSee('on_leave', false);
        $resp->assertSee('Mathematics', false);
        $resp->assertSee('G10', false);
    }

    public function test_admin_can_change_teacher_status_via_dropdown(): void
    {
        $entities = $this->seedTeacherWithData('active');
        $this->actingAs($entities['admin']);
        $teacher = $entities['teacher'];

        $resp = $this->put(route('teachers.status', $teacher), ['status' => 'on_leave']);
        $resp->assertRedirect();
        $teacher->refresh();
        $this->assertSame('on_leave', $teacher->account->status);
    }
}
