<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Account;
use App\Models\YearLevel;
use App\Models\YearLevelAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTeacherAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(): Account
    {
        return Account::create([
            'Username' => 'admin_'.uniqid(),
            'Password_Hash' => bcrypt('password'),
            'Email' => 'admin_'.uniqid().'@example.test',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    protected function makeTeacher(string $last = 'Teacher'): array
    {
        $acc = Account::create([
            'Username' => 'teacher_'.uniqid(),
            'Password_Hash' => bcrypt('password'),
            'Email' => 'teacher_'.uniqid().'@example.test',
            'role' => 'teacher',
            'status' => 'active',
        ]);
        $t = Teacher::create([
            'account_ID' => $acc->account_ID,
            'first_name' => 'Alice',
            'last_name' => $last,
            'department' => 'Science',
        ]);
        return ['account' => $acc, 'teacher' => $t];
    }

    public function test_bulk_assignments_succeed_with_qualification_and_grade(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);
        $yl = YearLevel::firstOrCreate(['grade_level' => 10]);
        $sub1 = Subject::create(['name' => 'Mathematics', 'description' => 'Math', 'grade_level' => 10]);
        $sub2 = Subject::create(['name' => 'Science', 'description' => 'Sci', 'grade_level' => 10]);
        $t1 = $this->makeTeacher('One')['teacher'];
        $t2 = $this->makeTeacher('Two')['teacher'];
        YearLevelAssignment::create(['teacher_ID' => $t1->teacher_ID, 'year_level_ID' => $yl->year_level_ID, 'status' => 'approved']);
        YearLevelAssignment::create(['teacher_ID' => $t2->teacher_ID, 'year_level_ID' => $yl->year_level_ID, 'status' => 'approved']);
        \Illuminate\Support\Facades\DB::table('teacher_subject_qualifications')->insert([
            'teacher_ID' => $t1->teacher_ID, 'subject_ID' => $sub1->subject_ID, 'created_at' => now(), 'updated_at' => now(),
        ]);
        \Illuminate\Support\Facades\DB::table('teacher_subject_qualifications')->insert([
            'teacher_ID' => $t2->teacher_ID, 'subject_ID' => $sub1->subject_ID, 'created_at' => now(), 'updated_at' => now(),
        ]);
        \Illuminate\Support\Facades\DB::table('teacher_subject_qualifications')->insert([
            'teacher_ID' => $t2->teacher_ID, 'subject_ID' => $sub2->subject_ID, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $resp = $this->post(route('subjects.assign-teachers.post'), [
            'grade_level' => 10,
            'subject_ids' => [$sub1->subject_ID, $sub2->subject_ID],
            'teacher_ids' => [$t1->teacher_ID, $t2->teacher_ID],
        ]);
        $resp->assertRedirect();
        $this->assertDatabaseHas('subject_teacher', ['subject_ID' => $sub1->subject_ID, 'teacher_ID' => $t1->teacher_ID]);
        $this->assertDatabaseHas('subject_teacher', ['subject_ID' => $sub1->subject_ID, 'teacher_ID' => $t2->teacher_ID]);
        $this->assertDatabaseHas('subject_teacher', ['subject_ID' => $sub2->subject_ID, 'teacher_ID' => $t2->teacher_ID]);
        $this->assertDatabaseMissing('subject_teacher', ['subject_ID' => $sub2->subject_ID, 'teacher_ID' => $t1->teacher_ID]);
    }

    public function test_prevents_duplicate_assignments(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);
        $yl = YearLevel::firstOrCreate(['grade_level' => 10]);
        $sub = Subject::create(['name' => 'Mathematics', 'description' => 'Math', 'grade_level' => 10]);
        $t = $this->makeTeacher('Dup')['teacher'];
        YearLevelAssignment::create(['teacher_ID' => $t->teacher_ID, 'year_level_ID' => $yl->year_level_ID, 'status' => 'approved']);
        \Illuminate\Support\Facades\DB::table('teacher_subject_qualifications')->insert([
            'teacher_ID' => $t->teacher_ID, 'subject_ID' => $sub->subject_ID, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->post(route('subjects.assign-teachers.post'), [
            'grade_level' => 10,
            'subject_ids' => [$sub->subject_ID],
            'teacher_ids' => [$t->teacher_ID],
        ])->assertRedirect();
        $this->post(route('subjects.assign-teachers.post'), [
            'grade_level' => 10,
            'subject_ids' => [$sub->subject_ID],
            'teacher_ids' => [$t->teacher_ID],
        ])->assertRedirect();
        $count = \Illuminate\Support\Facades\DB::table('subject_teacher')->where('subject_ID', $sub->subject_ID)->where('teacher_ID', $t->teacher_ID)->count();
        $this->assertSame(1, $count);
    }

    public function test_validation_rejects_unqualified_or_wrong_grade(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);
        $yl10 = YearLevel::firstOrCreate(['grade_level' => 10]);
        $yl9 = YearLevel::firstOrCreate(['grade_level' => 9]);
        $sub10 = Subject::create(['name' => 'Mathematics', 'description' => 'Math', 'grade_level' => 10]);
        $t = $this->makeTeacher('Three')['teacher'];
        YearLevelAssignment::create(['teacher_ID' => $t->teacher_ID, 'year_level_ID' => $yl9->year_level_ID, 'status' => 'approved']);
        $resp = $this->post(route('subjects.assign-teachers.post'), [
            'grade_level' => 10,
            'subject_ids' => [$sub10->subject_ID],
            'teacher_ids' => [$t->teacher_ID],
        ]);
        $resp->assertRedirect();
        $this->assertDatabaseMissing('subject_teacher', ['subject_ID' => $sub10->subject_ID, 'teacher_ID' => $t->teacher_ID]);
    }
}

