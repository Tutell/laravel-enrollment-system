<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentArchiveFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAccount(string $role = 'admin'): Account
    {
        return Account::create([
            'Username' => 'user_'.$role.'_'.uniqid(),
            'Password_Hash' => bcrypt('password'),
            'Email' => $role.'@example.test',
            'role' => $role,
            'status' => 'active',
        ]);
    }

    protected function makeStudent(array $overrides = []): Student
    {
        $data = array_merge([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'gender' => 'Male',
            'birthdate' => '2000-01-01',
            'status' => 'active',
            'email' => 'student@example.test',
            'phone' => '0000000000',
            'lrn' => '123456789012',
        ], $overrides);

        return Student::create($data);
    }

    public function test_admin_can_archive_student(): void
    {
        $admin = $this->makeAccount('admin');
        $student = $this->makeStudent();
        $this->actingAs($admin);

        $resp = $this->post(route('students.archive.post', $student), [
            'archive_reason' => 'Dropped by admin',
        ]);
        $resp->assertRedirect();
        $student->refresh();
        $this->assertSame('archived', $student->status);
        $this->assertNotNull($student->archived_at);
        $this->assertSame('Dropped by admin', $student->archive_reason);
    }

    public function test_teacher_can_archive_student(): void
    {
        $teacher = $this->makeAccount('teacher');
        $student = $this->makeStudent();
        $this->actingAs($teacher);

        $resp = $this->post(route('students.archive.post', $student), [
            'archive_reason' => 'Dropped by teacher',
        ]);
        $resp->assertRedirect();
        $student->refresh();
        $this->assertSame('archived', $student->status);
        $this->assertSame('Dropped by teacher', $student->archive_reason);
    }

    public function test_non_privileged_cannot_archive_student(): void
    {
        $user = $this->makeAccount('student');
        $student = $this->makeStudent();
        $this->actingAs($user);

        $resp = $this->post(route('students.archive.post', $student));
        $resp->assertStatus(403);
        $student->refresh();
        $this->assertSame('active', $student->status);
    }

    public function test_admin_can_restore_archived_student(): void
    {
        $admin = $this->makeAccount('admin');
        $student = $this->makeStudent(['status' => 'archived']);
        $this->actingAs($admin);

        $resp = $this->post(route('students.restore', $student));
        $resp->assertRedirect();
        $student->refresh();
        $this->assertSame('active', $student->status);
        $this->assertNull($student->archived_at);
        $this->assertNull($student->archive_reason);
    }

    public function test_teacher_cannot_restore_archived_student(): void
    {
        $teacher = $this->makeAccount('teacher');
        $student = $this->makeStudent(['status' => 'archived']);
        $this->actingAs($teacher);

        $resp = $this->post(route('students.restore', $student));
        $resp->assertStatus(403);
        $student->refresh();
        $this->assertSame('archived', $student->status);
    }

    public function test_students_index_has_archive_bin_drop_zone_and_draggable_rows(): void
    {
        $admin = $this->makeAccount('admin');
        $student = $this->makeStudent();
        $this->actingAs($admin);

        $resp = $this->get(route('students.index'));
        $resp->assertStatus(200);
        $resp->assertSee('id="archiveBinDropZone"', false);
        $resp->assertSee('class="align-middle draggable-student"', false);
        $resp->assertSee('draggable="true"', false);
        $resp->assertSee((string) $student->student_ID, false);
    }
}
