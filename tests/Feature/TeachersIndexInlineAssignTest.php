<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeachersIndexInlineAssignTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_inline_department_dropdown_and_updates_on_change()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        $dept = Department::create(['name' => 'Filipino', 'slug' => 'filipino']);
        $acct = Account::create(['Email' => 't@example.com', 'Username' => 'teach', 'Password_Hash' => Hash::make('x'), 'role' => 'teacher', 'status' => 'active']);
        $t = Teacher::create(['account_ID' => $acct->account_ID, 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'department' => 'Filipino']);

        $resp = $this->get(route('teachers.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Teachers');

        $resp = $this->put(route('teachers.department', $t), [
            'department_id' => $dept->department_ID,
        ]);
        $resp->assertRedirect(route('teachers.show', $t));
        $this->assertDatabaseHas('teachers', ['teacher_ID' => $t->teacher_ID, 'department_ID' => $dept->department_ID]);
    }
}
