<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountsLogsLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_manage_accounts_page_shows_view_logs_button_for_admin()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $this->actingAs($admin, 'web');
        $resp = $this->get(route('admin.accounts.index'));
        $resp->assertStatus(200);
        $resp->assertSee('View Logs');
        $resp->assertSee(route('admin.logs.index'), false);
    }
}
