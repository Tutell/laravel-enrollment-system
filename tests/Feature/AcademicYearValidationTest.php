<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_semester_fails_validation()
    {
        $admin = \App\Models\Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        $resp = $this->post(route('academic-years.store'), [
            'school_year' => '2025-2026',
            'semester' => '2nd Semeter',
            'is_active' => true,
        ]);
        $resp->assertSessionHasErrors(['semester']);
    }

    public function test_valid_semester_passes_and_creates_record()
    {
        $admin = \App\Models\Account::create([
            'Email' => 'admin2@example.com',
            'Username' => 'admin2',
            'Password_Hash' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        $resp = $this->post(route('academic-years.store'), [
            'school_year' => '2026-2027',
            'semester' => '2nd Semester',
            'is_active' => true,
        ]);
        $resp->assertRedirect();
        $this->assertDatabaseHas('academic_years', [
            'school_year' => '2026-2027',
            'semester' => '2nd Semester',
            'is_active' => 1,
        ]);
    }
}
