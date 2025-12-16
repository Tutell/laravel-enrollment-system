<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_redirects_to_login()
    {
        $account = Account::create([
            'Email' => 'user@example.com',
            'Username' => 'user1',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'student',
            'status' => 'active',
        ]);
        $this->actingAs($account, 'web');

        $response = $this->post(route('logout'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
