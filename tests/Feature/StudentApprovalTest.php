<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_student()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2005-01-01',
            'status' => 'pending',
            'lrn' => '123456789012',
        ]);

        $this->actingAs($admin, 'web');

        $response = $this->post(route('students.approve', $student));

        $response->assertRedirect(route('students.index'));
        $this->assertEquals('active', $student->fresh()->status);
    }
}
