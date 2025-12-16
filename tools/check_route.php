<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;

$s = Student::first();
if (! $s) {
    echo "No students found in DB\n";
    exit(0);
}

echo "Student as array:\n";
print_r($s->toArray());

echo 'student_id attribute: ';
var_export($s->student_id);
echo "\n";

try {
    $url = route('students.show', $s);
    echo "Generated URL: {$url}\n";
} catch (Exception $e) {
    echo 'Exception generating route: '.get_class($e).' - '.$e->getMessage()."\n";
}
