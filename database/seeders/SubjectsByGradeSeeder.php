<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectsByGradeSeeder extends Seeder
{
    public function run()
    {
        $names = [
            'Filipino',
            'English',
            'Math',
            'Science',
            'Araling Panlipunan (Social Studies)',
            'Music & Arts',
            'PE & Health',
            'Edukasyong Pantahanan at Pangkabuhayan (EPP)/Technology and Livelihood Education (TLE)',
        ];

        foreach ([7, 8, 9, 10] as $grade) {
            foreach ($names as $name) {
                Subject::updateOrCreate(
                    ['name' => $name, 'grade_level' => $grade],
                    ['description' => "Grade {$grade} {$name} curriculum"]
                );
            }
        }
    }
}
