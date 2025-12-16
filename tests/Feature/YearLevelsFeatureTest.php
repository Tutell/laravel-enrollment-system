<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class YearLevelsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_grade_cards()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');

        $resp = $this->get(route('year-levels.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Grade 7');
        $resp->assertSee('Grade 10');
    }
}
