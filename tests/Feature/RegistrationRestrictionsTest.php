<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationRestrictionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_register_route_is_disabled()
    {
        $this->get('/register')->assertStatus(404);
        $this->post('/register', [])->assertStatus(404);
    }

    public function test_pending_account_cannot_login()
    {
        $account = Account::create([
            'Email' => 'pending@example.com',
            'Username' => 'pending1',
            'Password_Hash' => Hash::make('secret'),
            'role' => 'student',
            'status' => 'pending',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'pending@example.com',
            'password' => 'secret',
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    public function test_admin_can_create_teacher_account()
    {
        $admin = Account::create([
            'Email' => 'admin@example.com',
            'Username' => 'admin',
            'Password_Hash' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin, 'web');

        $response = $this->post(route('admin.accounts.store'), [
            'username' => 'teach1',
            'email' => 'teach1@example.com',
            'password' => 'secret123',
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.accounts.index'));
        $this->assertDatabaseHas('accounts', [
            'Username' => 'teach1',
        ]);
    }
}
