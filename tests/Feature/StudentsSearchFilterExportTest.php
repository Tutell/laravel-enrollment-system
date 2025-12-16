<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentsSearchFilterExportTest extends TestCase
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

    public function test_search_by_name_and_filter_gender_status_section()
    {
        $this->loginAdmin();

        $sec9A = Section::create(['section_name' => 'Grade 9-A', 'grade_level' => 9, 'capacity' => 40]);
        $sec10B = Section::create(['section_name' => 'Grade 10-B', 'grade_level' => 10, 'capacity' => 40]);

        $a1 = Account::create(['Email' => 's1@example.com', 'Username' => 's1', 'Password_Hash' => Hash::make('x'), 'role' => 'student', 'status' => 'active']);
        $a2 = Account::create(['Email' => 's2@example.com', 'Username' => 's2', 'Password_Hash' => Hash::make('x'), 'role' => 'student', 'status' => 'active']);
        $a3 = Account::create(['Email' => 's3@example.com', 'Username' => 's3', 'Password_Hash' => Hash::make('x'), 'role' => 'student', 'status' => 'active']);

        $s1 = Student::create(['account_ID' => $a1->account_ID, 'section_ID' => $sec9A->section_ID, 'first_name' => 'Alice', 'last_name' => 'Wonder', 'gender' => 'Female', 'status' => 'active', 'birthdate' => '2008-01-01', 'lrn' => '100000000001']);
        $s2 = Student::create(['account_ID' => $a2->account_ID, 'section_ID' => $sec10B->section_ID, 'first_name' => 'Bob', 'last_name' => 'Builder', 'gender' => 'Male', 'status' => 'inactive', 'birthdate' => '2008-02-02', 'lrn' => '100000000002']);
        $s3 = Student::create(['account_ID' => $a3->account_ID, 'section_ID' => $sec10B->section_ID, 'first_name' => 'Carol', 'last_name' => 'Singer', 'gender' => 'Female', 'status' => 'pending', 'birthdate' => '2008-03-03', 'lrn' => '100000000003']);

        $resp = $this->get(route('students.index', ['q' => 'Ali']));
        $resp->assertStatus(200)->assertSee('Alice')->assertDontSee('Bob')->assertDontSee('Carol');

        $resp = $this->get(route('students.index', ['gender' => 'Female']));
        $resp->assertStatus(200)->assertSee('Alice')->assertSee('Carol')->assertDontSee('Bob');

        $resp = $this->get(route('students.index', ['status' => 'inactive']));
        $resp->assertStatus(200)->assertSee('Bob')->assertDontSee('Alice');

        $resp = $this->get(route('students.index', ['section_id' => $sec9A->section_ID]));
        $resp->assertStatus(200)->assertSee('Alice')->assertDontSee('Bob');

        $resp = $this->get(route('students.index', ['grade_level' => 10]));
        $resp->assertStatus(200)->assertSee('Bob')->assertSee('Carol')->assertDontSee('Alice');
    }

    public function test_export_csv_respects_filters()
    {
        $this->loginAdmin();

        $sec9A = Section::create(['section_name' => 'Grade 9-A', 'grade_level' => 9, 'capacity' => 40]);

        $a1 = Account::create(['Email' => 's1@example.com', 'Username' => 's1', 'Password_Hash' => Hash::make('x'), 'role' => 'student', 'status' => 'active']);
        $a2 = Account::create(['Email' => 's2@example.com', 'Username' => 's2', 'Password_Hash' => Hash::make('x'), 'role' => 'student', 'status' => 'active']);

        $s1 = Student::create(['account_ID' => $a1->account_ID, 'section_ID' => $sec9A->section_ID, 'first_name' => 'Alice', 'last_name' => 'Wonder', 'gender' => 'Female', 'status' => 'active', 'birthdate' => '2008-01-01', 'lrn' => '200000000001']);
        $s2 = Student::create(['account_ID' => $a2->account_ID, 'section_ID' => $sec9A->section_ID, 'first_name' => 'Bob', 'last_name' => 'Builder', 'gender' => 'Male', 'status' => 'inactive', 'birthdate' => '2008-02-02', 'lrn' => '200000000002']);

        $resp = $this->get(route('students.export', ['status' => 'active']));
        $resp->assertStatus(200);
        $text = $resp->getContent();
        $this->assertStringContainsString('Alice', $text);
        $this->assertStringNotContainsString('Bob', $text);
    }
}
