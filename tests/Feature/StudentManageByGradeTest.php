<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentManageByGradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_manage_page_grouped()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        $sec = Section::create(['section_name' => 'Section A', 'grade_level' => 7, 'capacity' => 40]);
        Student::create(['first_name'=>'A','last_name'=>'One','gender'=>'Male','birthdate'=>now()->subYears(13)->toDateString(),'lrn'=>'111111111111','section_ID'=>$sec->section_ID]);

        $resp = $this->get(route('students.manage'));
        $resp->assertStatus(200);
        $resp->assertSee('Grade 7');
        $resp->assertSee('Section A');
    }

    public function test_teacher_sees_only_assigned_sections()
    {
        $acc = Account::create([
            'Email' => 't@example.com',
            'Username' => 'teacher',
            'Password_Hash' => bcrypt('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);
        $this->actingAs($acc, 'web');
        $teacher = Teacher::create(['account_ID'=>$acc->account_ID,'first_name'=>'T','last_name'=>'E','contact_number'=>'','department'=>'Math']);

        $secA = Section::create(['section_name' => 'Sec A', 'grade_level' => 7, 'capacity' => 40, 'teacher_ID'=>$teacher->teacher_ID]);
        $secB = Section::create(['section_name' => 'Sec B', 'grade_level' => 7, 'capacity' => 40]);
        Student::create(['first_name'=>'S','last_name'=>'A','gender'=>'Male','birthdate'=>now()->subYears(13)->toDateString(),'lrn'=>'222222222222','section_ID'=>$secA->section_ID]);
        Student::create(['first_name'=>'S','last_name'=>'B','gender'=>'Male','birthdate'=>now()->subYears(13)->toDateString(),'lrn'=>'333333333333','section_ID'=>$secB->section_ID]);

        $resp = $this->get(route('students.manage'));
        $resp->assertStatus(200);
        $resp->assertSee('Sec A');
        $resp->assertDontSee('Sec B');
    }
}

