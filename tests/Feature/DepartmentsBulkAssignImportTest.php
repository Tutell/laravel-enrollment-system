<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DepartmentsBulkAssignImportTest extends TestCase
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

    public function test_bulk_assign_updates_teachers_department()
    {
        $this->loginAdmin();
        $dept = Department::create(['name' => 'Science', 'slug' => 'science']);
        $acct1 = Account::create(['Email' => 'a1@example.com', 'Username' => 't1', 'Password_Hash' => Hash::make('x'), 'role' => 'teacher', 'status' => 'active']);
        $acct2 = Account::create(['Email' => 'a2@example.com', 'Username' => 't2', 'Password_Hash' => Hash::make('x'), 'role' => 'teacher', 'status' => 'active']);
        $t1 = Teacher::create(['account_ID' => $acct1->account_ID, 'first_name' => 'A', 'last_name' => 'One', 'department' => 'Science']);
        $t2 = Teacher::create(['account_ID' => $acct2->account_ID, 'first_name' => 'B', 'last_name' => 'Two', 'department' => 'Science']);

        $resp = $this->post(route('admin.departments.bulk.post'), [
            'department_id' => $dept->department_ID,
            'teacher_ids' => [$t1->teacher_ID, $t2->teacher_ID],
        ]);
        $resp->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('teachers', ['teacher_ID' => $t1->teacher_ID, 'department_ID' => $dept->department_ID]);
        $this->assertDatabaseHas('teachers', ['teacher_ID' => $t2->teacher_ID, 'department_ID' => $dept->department_ID]);
    }

    public function test_import_csv_assigns_teachers()
    {
        $this->loginAdmin();
        $dept = Department::create(['name' => 'Math', 'slug' => 'math']);
        $acct = Account::create(['Email' => 'teacher@example.com', 'Username' => 'teach', 'Password_Hash' => Hash::make('x'), 'role' => 'teacher', 'status' => 'active']);
        $t = Teacher::create(['account_ID' => $acct->account_ID, 'first_name' => 'John', 'last_name' => 'Doe', 'department' => 'Math']);

        $csvContent = "username,email,department\nteach,teacher@example.com,Math\n";
        $file = UploadedFile::fake()->createWithContent('teachers.csv', $csvContent);

        $resp = $this->post(route('admin.departments.import.post'), [
            'file' => $file,
        ]);
        $resp->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('teachers', ['teacher_ID' => $t->teacher_ID, 'department_ID' => $dept->department_ID]);
    }
}
