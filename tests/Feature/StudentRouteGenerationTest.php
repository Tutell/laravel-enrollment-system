<?php

namespace Tests\Feature;

use App\Models\Student;
use Tests\TestCase;

class StudentRouteGenerationTest extends TestCase
{
    /**
     * Ensure route generation uses the student route key and accessors work.
     *
     * @return void
     */
    public function test_route_generation_with_model_uses_student_id_and_accessor()
    {
        // Create a non-persisted model instance and assign attributes directly
        $student = new Student;
        $student->student_ID = 123;
        $student->account_ID = 3;
        $student->section_ID = 1;
        $student->first_name = 'Auto';
        $student->last_name = 'Tester';

        // Ensure both attribute styles resolve
        $this->assertEquals(123, $student->student_ID);
        $this->assertEquals(123, $student->student_id);

        // Route generation should work without persisting the model
        $url = route('students.show', $student);
        $this->assertStringContainsString('/students/123', $url);
    }
}
