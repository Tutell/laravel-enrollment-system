<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class DefaultSectionsSeeder extends Seeder
{
    public function run()
    {
        foreach ([7, 8, 9, 10] as $grade) {
            foreach (['A', 'B', 'C'] as $letter) {
                Section::updateOrCreate(
                    ['section_name' => "Grade {$grade}-{$letter}", 'grade_level' => $grade],
                    ['capacity' => 40]
                );
            }
        }
    }
}
