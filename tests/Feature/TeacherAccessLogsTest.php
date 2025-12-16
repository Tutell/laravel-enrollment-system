<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherAccessLogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_login_records_log_and_student_does_not()
    {
        $teacherAccount = Account::create([
            'Email' => 't@example.com',
            'Username' => 'teacher1',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'teacher',
        ]);
        Teacher::create([
            'account_ID' => $teacherAccount->account_ID,
            'first_name' => 'Teach',
            'last_name' => 'Er',
            'department' => 'Math',
        ]);

        $studentAccount = Account::create([
            'Email' => 's@example.com',
            'Username' => 'student1',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'student',
        ]);

        $resp = $this->post(route('login.post'), ['username' => 'teacher1', 'password' => 'secret']);
        $resp->assertRedirect();
        $this->assertDatabaseHas('teacher_access_logs', [
            'account_ID' => $teacherAccount->account_ID,
            'action' => 'Login',
        ]);

        $this->post(route('logout'));
        $this->assertDatabaseHas('teacher_access_logs', [
            'account_ID' => $teacherAccount->account_ID,
            'action' => 'Logout',
        ]);

        $resp2 = $this->post(route('login.post'), ['username' => 'student1', 'password' => 'secret']);
        $resp2->assertRedirect();
        $this->assertDatabaseMissing('teacher_access_logs', [
            'account_ID' => $studentAccount->account_ID,
        ]);
    }

    public function test_admin_can_view_logs_and_non_admin_cannot()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'web');
        $resp = $this->get(route('admin.logs.index'));
        $resp->assertStatus(200);

        $student = Account::create([
            'Email' => 's2@example.com',
            'Username' => 'student2',
            'Password_Hash' => Hash::make('password'),
            'role' => 'student',
        ]);
        $this->actingAs($student, 'web');
        $resp2 = $this->get(route('admin.logs.index'));
        $resp2->assertStatus(403);
    }
}
