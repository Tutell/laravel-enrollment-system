<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Account;
use App\Models\AccountAudit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AcademicYearsStudentGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_is_redirected_and_logged_when_accessing_academic_years()
    {
        $account = Account::create([
            'Email' => 'stu@example.com',
            'Username' => 'stu',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'student',
            'status' => 'active',
        ]);
        AcademicYear::create([
            'school_year' => '2025-2026',
            'semester' => '1st Semester',
            'is_active' => true,
        ]);

        $this->actingAs($account, 'web');
        $resp = $this->get(route('academic-years.index'));
        $resp->assertRedirect(route('student.dashboard'));
        $resp->assertSessionHasErrors();

        $this->assertTrue(AccountAudit::where('action', 'unauthorized_access')->exists());
    }
}
