<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DepartmentsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_shows_departments_link()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');
        $resp = $this->get(route('dashboard.index'));
        $resp->assertSee('Departments');
    }

    public function test_departments_index_lists_departments()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        Department::create(['name' => 'Math', 'slug' => 'math']);
        Department::create(['name' => 'Science', 'slug' => 'science']);

        $resp = $this->get(route('departments.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Math');
        $resp->assertSee('Science');
    }

    public function test_department_show_lists_teachers_with_pagination()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        $dept = Department::create(['name' => 'Math', 'slug' => 'math']);

        for ($i = 0; $i < 60; $i++) {
            $acct = Account::create([
                'Email' => "t{$i}@example.com",
                'Username' => "teacher{$i}",
                'Password_Hash' => Hash::make('password'),
                'role' => 'teacher',
                'status' => 'active',
            ]);
            Teacher::create([
                'account_ID' => $acct->account_ID,
                'department_ID' => $dept->department_ID,
                'first_name' => 'T'.$i,
                'last_name' => 'L'.$i,
                'contact_number' => '091234567'.($i % 10),
                'department' => 'Math',
            ]);
        }

        $resp = $this->get(route('departments.show', $dept));
        $resp->assertStatus(200);
        $resp->assertSee('Math');
        $resp->assertSee('Teachers');
        $resp->assertSee('Next');
    }
}
