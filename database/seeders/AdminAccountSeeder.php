<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminAccountSeeder extends Seeder
{
    public function run()
    {
        $admin = Account::updateOrCreate(
            ['Email' => 'admin@example.com'],
            [
                'Username' => 'admin',
                'Password_Hash' => bcrypt('password123'), // change this
                'role' => 'admin',
            ]
        );

        // optional: attach role via pivot, only if schema aligns
        // $role = Role::where('role_name','admin')->first();
        // if ($role) {
        //     $admin->roles()->syncWithoutDetaching([$role->getKey()]);
        // }
    }
}
