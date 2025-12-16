<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'teacher', 'student', 'student_portal_readonly', 'parent', 'faculty'];
        foreach ($roles as $r) {
            Role::updateOrCreate(['role_name' => $r]);
        }
    }
}
