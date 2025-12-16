<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Account;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use App\Models\AccountAudit;

class StudentLrnImmutabilityTest extends TestCase
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

    public function test_update_rejects_lrn_modification_with_400_and_logs_audit()
    {
        $this->loginAdmin();

        $student = Student::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'gender' => 'Male',
            'birthdate' => now()->subYears(12)->toDateString(),
            'lrn' => '123456789012',
        ]);

        $resp = $this->patch(route('students.update', $student), [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'gender' => 'Male',
            'birthdate' => now()->subYears(12)->toDateString(),
            'lrn' => '999999999999',
        ], ['Accept' => 'application/json']);

        $resp->assertStatus(400);
        $resp->assertJson([
            'error' => 'LRN cannot be modified after initial registration',
        ]);

        $this->assertTrue(AccountAudit::where('action','lrn_modification_attempt')->exists());
    }

    public function test_update_accepts_other_field_changes_without_lrn()
    {
        $this->loginAdmin();

        $student = Student::create([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'gender' => 'Female',
            'birthdate' => now()->subYears(11)->toDateString(),
            'lrn' => '111122223333',
        ]);

        $resp = $this->patch(route('students.update', $student), [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'gender' => 'Female',
            'birthdate' => now()->subYears(11)->toDateString(),
            'phone' => '09171234567',
        ]);

        $resp->assertRedirect(route('students.show', $student));

        $student->refresh();
        $this->assertSame('111122223333', $student->lrn);
    }
}

