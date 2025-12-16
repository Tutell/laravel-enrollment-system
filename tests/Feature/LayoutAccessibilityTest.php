<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LayoutAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_has_skip_link_and_main_role(): void
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($admin, 'web');

        $resp = $this->get(route('dashboard.index'));
        $resp->assertStatus(200);
        $resp->assertSee('Skip to content', false);
        $resp->assertSee('role="main"', false);
        $resp->assertSee('aria-label="Sidebar Navigation"', false);
    }
}
