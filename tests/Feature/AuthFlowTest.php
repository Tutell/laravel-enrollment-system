<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $account = Account::create([
            'Email' => 'user@example.com',
            'Username' => 'user1',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'student',
        ]);

        $this->actingAs($account, 'web');

        $response = $this->get('/dashboard');
        $response->assertOk();
    }

    public function test_login_redirects_to_dashboard(): void
    {
        $account = Account::create([
            'Email' => 'login@example.com',
            'Username' => 'user2',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'student',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'login@example.com',
            'password' => 'secret',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($account, 'web');
    }
}
