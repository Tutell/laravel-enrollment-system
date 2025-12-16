<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DepartmentsSeeder::class,
            RolesSeeder::class,
            AdminAccountSeeder::class,
            DefaultSectionsSeeder::class,
            SampleDataSeeder::class,
            SubjectsByGradeSeeder::class,
            TeachersDepartmentBackfillSeeder::class,
        ]);
    }
}
