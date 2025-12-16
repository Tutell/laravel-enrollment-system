<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BrandingSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_branding()
    {
        $admin = Account::create([
            'Username' => 'adminuser',
            'Email' => 'admin@example.com',
            'Password_Hash' => bcrypt('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $resp = $this->post(route('admin.branding.update'), [
            'system_name' => 'My School',
            'welcome_message' => 'Welcome to My School',
            'subtext' => '<strong>Transforming education</strong>',
            'school_name' => 'My School Inc.',
            'mission' => 'Mission text',
            'vision' => 'Vision text',
            'core_values' => 'Values text',
        ]);

        $resp->assertRedirect(route('admin.branding.show'));

        $branding = BrandingSetting::first();
        $this->assertNotNull($branding);
        $this->assertSame('My School', $branding->system_name);
        $this->assertSame('Welcome to My School', $branding->welcome_message);
        $this->assertSame('<strong>Transforming education</strong>', $branding->subtext);
        $this->assertSame('My School Inc.', $branding->school_name);
    }
}

