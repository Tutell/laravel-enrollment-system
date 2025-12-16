<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentsSeeder extends Seeder
{
    public function run()
    {
        $names = ['Math', 'Science', 'English', 'Filipino', 'Humanities', 'Physical Education', 'ICT'];
        foreach ($names as $name) {
            $base = Str::slug($name);
            $slug = $base;
            $i = 1;
            while (Department::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i;
                $i++;
            }
            Department::firstOrCreate(['name' => $name], ['slug' => $slug]);
        }
    }
}
