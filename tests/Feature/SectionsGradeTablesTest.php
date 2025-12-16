<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SectionsGradeTablesTest extends TestCase
{
    use RefreshDatabase;

    public function test_grade_tables_render_with_dropdowns()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        Section::create(['section_name' => 'Grade 7-A', 'grade_level' => 7, 'capacity' => 40]);
        Section::create(['section_name' => 'Grade 8-A', 'grade_level' => 8, 'capacity' => 40]);

        $resp = $this->get(route('sections.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Grade 7');
        $resp->assertSee('Grade 8');
        $resp->assertSee('Grade 7-A');
        $resp->assertSee('Grade 8-A');
        $resp->assertSee('sectionSelect7', false);
        $resp->assertSee('sectionSelect8', false);
    }
}
