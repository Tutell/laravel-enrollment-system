<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentDashboardSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_nav_is_restricted_and_no_sidebar()
    {
        $account = Account::create([
            'Email' => 'stu@example.com',
            'Username' => 'stu1',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $sec9 = Section::create(['section_name' => 'Grade 9-A', 'grade_level' => 9, 'capacity' => 40]);
        $student = Student::create([
            'account_ID' => $account->account_ID,
            'section_ID' => $sec9->section_ID,
            'first_name' => 'Stu',
            'last_name' => 'Dent',
            'gender' => 'Male',
            'birthdate' => '2008-01-01',
            'lrn' => '100000000009',
        ]);

        $this->actingAs($account, 'web');
        $resp = $this->get(route('student.dashboard'));
        $resp->assertStatus(200);
        $html = $resp->getContent();

        $this->assertStringContainsString('Dashboard', $html);
        $this->assertStringContainsString('Logout', $html);
        $this->assertStringContainsString('Profile', $html);

        $this->assertStringNotContainsString('Subjects</a>', $html);
        $this->assertStringNotContainsString('Sections</a>', $html);

        $this->assertStringNotContainsString('sidebar-container', $html);
    }

    public function test_student_sees_only_sections_for_own_grade()
    {
        $account = Account::create([
            'Email' => 'stu2@example.com',
            'Username' => 'stu2',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $sec9A = Section::create(['section_name' => 'Grade 9-A', 'grade_level' => 9, 'capacity' => 40]);
        $sec10B = Section::create(['section_name' => 'Grade 10-B', 'grade_level' => 10, 'capacity' => 40]);

        $student = Student::create([
            'account_ID' => $account->account_ID,
            'section_ID' => $sec9A->section_ID,
            'first_name' => 'Learner',
            'last_name' => 'Nine',
            'gender' => 'Male',
            'birthdate' => '2008-01-01',
            'lrn' => '100000000010',
        ]);

        $this->actingAs($account, 'web');
        $resp = $this->get(route('student.dashboard'));
        $resp->assertStatus(200);
        $html = $resp->getContent();

        $this->assertStringContainsString('Grade 9', $html);
        $this->assertStringContainsString('Grade 9-A', $html);
        $this->assertStringNotContainsString('Grade 10-B', $html);
    }
}
