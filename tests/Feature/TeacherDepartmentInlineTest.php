<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherDepartmentInlineTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_change_teacher_department_inline()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        $dept1 = Department::create(['name' => 'English', 'slug' => 'english']);
        $dept2 = Department::create(['name' => 'Science', 'slug' => 'science']);
        $acct = Account::create(['Email' => 't@example.com', 'Username' => 'teach', 'Password_Hash' => Hash::make('x'), 'role' => 'teacher', 'status' => 'active']);
        $teacher = Teacher::create(['account_ID' => $acct->account_ID, 'first_name' => 'A', 'last_name' => 'B', 'department' => 'English', 'department_ID' => $dept1->department_ID]);

        $resp = $this->put(route('teachers.department', $teacher), [
            'department_id' => $dept2->department_ID,
        ]);
        $resp->assertRedirect(route('teachers.show', $teacher));
        $this->assertDatabaseHas('teachers', [
            'teacher_ID' => $teacher->teacher_ID,
            'department_ID' => $dept2->department_ID,
        ]);
    }
}
