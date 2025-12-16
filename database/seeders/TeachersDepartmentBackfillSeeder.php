<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeachersDepartmentBackfillSeeder extends Seeder
{
    public function run()
    {
        $map = Department::all()->mapWithKeys(function ($d) {
            return [strtolower($d->name) => $d->department_ID];
        });

        Teacher::chunk(200, function ($teachers) use ($map) {
            foreach ($teachers as $t) {
                if (! $t->department_ID) {
                    $name = strtolower(trim((string) $t->department));
                    if ($name && isset($map[$name])) {
                        $t->department_ID = $map[$name];
                        $t->save();
                    }
                }
            }
        });
    }
}
